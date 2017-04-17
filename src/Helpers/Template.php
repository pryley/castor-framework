<?php

namespace GeminiLabs\Castor\Helpers;

use GeminiLabs\Castor\Facades\Development;
use GeminiLabs\Castor\Facades\Utility;

class Template
{
	/**
	 * @var string
	 */
	public $template;

	/**
	 * @param string $slug
	 * @param string $name
	 *
	 * @return string
	 */
	public function get( $slug, $name = '' )
	{
		$template  = Utility::startWith( 'templates/', $slug );
		$templates = ["{$template}.php"];

		if( 'index' != basename( $this->template, '.php' )) {
			$filepath = dirname( $template ) != '.'
				? sprintf( '%s/', dirname( $template ))
				: '';
			array_unshift(
				$templates,
				sprintf( '%s%s-%s.php', $filepath, $name, basename( $template ))
			);
		}

		$templates = apply_filters( "castor/templates/{$slug}", $templates, $name );

		return locate_template( $templates );
	}

	/**
	 * @param string $slug
	 * @param string $name
	 *
	 * @return void
	 */
	public function load( $slug, $name = '' )
	{
		$template = $this->get( $slug, $name );
		if( !empty( $template )) {
			Development::storeTemplatePath( $template );
			load_template( $template, false );
		}
	}

	/**
	 * @return void
	 */
	public function main()
	{
		$this->load( $this->template );
	}

	/**
	 * @param string $template
	 *
	 * @return string|void
	 */
	public function setLayout( $template )
	{
		$this->template = Utility::trimRight( strstr( $template, 'templates/' ), '.php' );
		return $this->get( apply_filters( 'castor/templates/layout', 'layouts/default' ));
	}
}
