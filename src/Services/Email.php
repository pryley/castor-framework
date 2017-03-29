<?php

namespace GeminiLabs\Castor\Services;

use GeminiLabs\Castor\Helpers\Template;

class Email
{
	/**
	 * @var Template
	 */
	public $template;

	/**
	 * @var array
	 */
	protected $attachments;

	/**
	 * @var array
	 */
	protected $headers;

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * @var string
	 */
	protected $to;

	public function __construct( Template $template )
	{
		$this->template = $template;
	}

	/**
	 * @return Email
	 */
	public function compose( array $email )
	{
		$email = $this->normalize( $email );

		$this->attachments = $email['attachments'];
		$this->headers     = $this->buildHeaders( $email );
		$this->message     = $this->buildHtmlMessage( $email );
		$this->subject     = $email['subject'];
		$this->to          = $email['to'];

		add_action( 'phpmailer_init', function( PHPMailer $phpmailer ) {
			if( $phpmailer->ContentType === 'text/plain' || !empty( $phpmailer->AltBody ))return;
			$phpmailer->AltBody = $this->buildPlainTextMessage( $phpmailer->Body );
		});

		return $this;
	}

	/**
	 * @param bool $plaintext
	 *
	 * @return string|null
	 */
	public function read( $plaintext = false )
	{
		return $plaintext
			? $this->buildPlainTextMessage( $this->message )
			: $this->message;
	}

	/**
	 * @return bool|null
	 */
	public function send()
	{
		if( !$this->message || !$this->subject || !$this->to )return;

		$sent = wp_mail(
			$this->to,
			$this->subject,
			$this->message,
			$this->headers,
			$this->attachments
		);

		$this->reset();

		return $sent;
	}

	/**
	 * @return array
	 */
	protected function buildHeaders( array $email )
	{
		$allowed = [
			'bcc',
			'cc',
			'from',
			'reply-to',
		];

		$headers = array_intersect_key( $email, array_flip( $allowed ));
		$headers = array_filter( $headers );

		foreach( $headers as $key => $value ) {
			unset( $headers[ $key ] );
			$headers[] = sprintf( '%s: %s', $key, $value );
		}

		$headers[] = 'Content-Type: text/html';

		return apply_filters( 'castor/email/headers', $headers, $this );
	}

	/**
	 * @return string
	 */
	protected function buildHtmlMessage( array $email )
	{
		$body = $this->renderTemplate( 'email' );

		$message = !empty( $email['template'] )
			? $this->renderTemplate( $email['template'], $email['template-tags'] )
			: $email['message'];

		$message = $this->filterHtml( $email['before'] . $message . $email['after'] );
		$message = str_replace( '{message}', $message, $body );

		return apply_filters( 'castor/email/message', $message, 'html', $this );
	}

	/**
	 * @param string $message
	 *
	 * @return string
	 */
	protected function buildPlainTextMessage( $message )
	{
		return apply_filters( 'castor/email/message', $this->stripHtmlTags( $message ), 'text', $this );
	}

	/**
	 * @param string $message
	 *
	 * @return string
	 */
	protected function filterHtml( $message )
	{
		$message = strip_shortcodes( $message );
		$message = wptexturize( $message );
		$message = wpautop( $message );
		$message = str_replace( ['&lt;&gt; ', ']]>'], ['', ']]&gt;'], $message );
		$message = stripslashes( $message );
		return $message;
	}

	/**
	 * @return string
	 */
	protected function getDefaultFrom()
	{
		return sprintf( '%s <%s>',
			wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
			get_option( 'admin_email' )
		);
	}

	/**
	 * @return array
	 */
	protected function normalize( $email )
	{
		$defaults = array_fill_keys([
			'after', 'attachments', 'bcc', 'before', 'cc', 'from', 'message', 'reply-to', 'subject',
			'template', 'template-tags', 'to',
		], '' );
		$defaults['from'] = $this->getDefaultFrom();

		$email = shortcode_atts( $defaults, $email );

		foreach( ['attachments', 'template-tags'] as $key ) {
			$email[$key] = array_filter( (array) $email[$key] );
		}
		if( empty( $email['reply-to'] )) {
			$email['reply-to'] = $email['from'];
		}

		return apply_filters( 'castor/email/compose', $email, $this );
	}

	/**
	 * Override by adding the custom template to "templates/castor/" in your theme
	 *
	 * @param string $templatePath
	 *
	 * @return void|string
	 */
	protected function renderTemplate( $templatePath, array $args = [] )
	{
		$file = $this->template->get( sprintf( 'castor/%s', $templatePath ));

		if( !file_exists( $file )) {
			$file = sprintf( '%s/templates/%s.php', dirname( __DIR__ ), $templatePath );
		}
		if( !file_exists( $file ))return;

		ob_start();
		include $file;
		$template = ob_get_clean();

		return $this->renderTemplateString( $template, $args );
	}

	/**
	 * @param string $template
	 *
	 * @return string
	 */
	protected function renderTemplateString( $template, array $args = [] )
	{
		foreach( $args as $key => $value ) {
			$template = str_replace( sprintf( '{%s}', $key ), $value, $template );
		}
		return trim( $template );
	}

	/**
	 * @return void
	 */
	protected function reset()
	{
		$this->attachments = [];
		$this->headers = [];
		$this->message = null;
		$this->subject = null;
		$this->to = null;
	}

	/**
	 * - remove invisible elements
	 * - replace certain elements with a line-break
	 * - replace certain table elements with a space
	 * - add a placeholder for plain-text bullets to list elements
	 * - strip all remaining HTML tags
	 * @return string
	 */
	protected function stripHtmlTags( $string )
	{
		$string = preg_replace( '@<(embed|head|noembed|noscript|object|script|style)[^>]*?>.*?</\\1>@siu', '', $string );
		$string = preg_replace( '@</(div|h[1-9]|p|pre|tr)@iu', "\r\n\$0", $string );
		$string = preg_replace( '@</(td|th)@iu', " \$0", $string );
		$string = preg_replace( '@<(li)[^>]*?>@siu', "\$0-o-^-o-", $string );
		$string = wp_strip_all_tags( $string );
		$string = wp_specialchars_decode( $string, ENT_QUOTES );
		$string = preg_replace( '/\v(?:[\v\h]+){2,}/', "\r\n\r\n", $string );
		$string = str_replace( '-o-^-o-', ' - ', $string );
		return html_entity_decode( $string, ENT_QUOTES, 'UTF-8' );
	}
}
