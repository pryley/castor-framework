<?php

namespace GeminiLabs\Castor;

use GeminiLabs\Castor\Helpers\Utility;
use DomDocument;

class Oembed
{
	public $oembed;
	public $utility;

	public $vimeo = [
		'api', 'autopause', 'autoplay', 'byline', 'color', 'height', 'loop', 'player_id',
		'portrait', 'title', 'width',
	];

	public $youtube = [
		'autohide', 'autoplay', 'cc_load_policy', 'color', 'controls', 'disablekb', 'enablejsapi',
		'end', 'fs', 'height', 'hl', 'iv_load_policy', 'list', 'listType', 'loop', 'modestbranding',
		'origin', 'playerapiid', 'playlist', 'playsinline', 'rel', 'showinfo', 'start', 'theme',
		'width',
	];

	public function __construct( Utility $utility )
	{
		$this->oembed  = _wp_oembed_get_object();
		$this->utility = $utility;
	}

	public function request( $url, $args = '' )
	{
		$request = $this->oembed->fetch( $this->oembed->get_provider( $url ), $url, [
			'width'  => 1280,
			'height' => 1280,
		]);
		if( $request ) {
			return $this->modifyRequest( $request, $args );
		}
	}

	protected function domLoad( $html )
	{
		$dom = new DomDocument;
		$dom->loadHTML( $html );
		return $dom;
	}

	protected function modifyRequest( $request, $args )
	{
		$providerName = strtolower( $request->provider_name );
		$provider = property_exists( $this, $providerName )
			? $this->$providerName
			: [];

		$method = $this->utility->buildMethodName( $providerName . '_request', 'modify' );

		if( method_exists( $this, $method )) {
			return call_user_func( [$this, $method], $request, array_intersect_key(
				wp_parse_args( $args ),
				array_flip( $provider )
			));
		}
		return $request;
	}

	protected function modifyYoutubeRequest( $request, array $args )
	{
		$html = $this->domLoad( $request->html );
		$node = $html->getElementsByTagName( 'iframe' )->item(0);
		$url  = $node->getAttribute( 'src' );

		if( isset( $args['fs'] ) && $args['fs'] == 0 ) {
			$node->removeAttribute( 'allowfullscreen' );
		}

		$args['origin'] = urlencode( get_bloginfo( 'url' ));

		$node->setAttribute( 'class', 'video-embed' );
		$node->setAttribute( 'src',
			add_query_arg( $args, remove_query_arg( 'feature', $url ))
		);

		$request->html = $html->saveHTML( $node );

		return $request;
	}
}
