<?php

namespace GeminiLabs\Castor;

use GeminiLabs\Castor\Helpers\PostMeta;
use GeminiLabs\Castor\Helpers\Theme;
use GeminiLabs\Castor\Helpers\Utility;
use GeminiLabs\Castor\Image;
use GeminiLabs\Castor\Oembed;

class Video
{
	public $image;
	public $postmeta;
	public $oembed;
	public $theme;
	public $utility;

	protected $args;
	protected $video;

	public function __construct( Image $image, Oembed $oembed, PostMeta $postmeta, Theme $theme, Utility $utility )
	{
		$this->image    = $image;
		$this->oembed   = $oembed;
		$this->postmeta = $postmeta;
		$this->theme    = $theme;
		$this->utility  = $utility;
	}

	public function get( array $args = [] )
	{
		$args = $this->normalize( $args );
		$this->video = $this->oembed->request( $args['url'], $args['player'] );
		return $this;
	}

	public function renderPlayButton()
	{
		return sprintf(
			'<div class="video-play">' .
				'<div class="video-play-pulse pulse1"></div>' .
				'<div class="video-play-pulse pulse2"></div>' .
				'<div class="video-play-pulse pulse3"></div>' .
				'<a href="%s" class="video-play-btn">%s</a>' .
			'</div>',
			$this->args['url'],
			$this->theme->svg( 'play.svg' )
		);
	}

	public function renderScreenshot()
	{
		if( !$this->args['image'] )return;
		return sprintf( '<div class="video-screenshot" style="background-image: url(%s)">%s</div>',
			$this->args['image'],
			$this->renderPlayButton()
		);
	}

	public function render()
	{
		if( !isset( $this->video->html ))return;
		return sprintf(
			'<div class="video embed">%s%s</div>',
			$this->renderScreenshot(),
			$this->video->html
		);
	}

	protected function setImage( $image )
	{
		$image = $this->image->get( $image );
		$this->args['image'] = isset( $image->large )
			? $image->large['url']
			: null;
	}

	protected function setUrl( $url )
	{
		$this->args['url'] = !filter_var( $url, FILTER_VALIDATE_URL )
			? $this->postmeta->get( $url )
			: $url;
	}

	protected function normalize( array $args = [] )
	{
		$this->args = shortcode_atts([
			'image'  => '', // string || int
			'player' => '', // string || array
			'url'    => '', // string
		], $args );

		foreach( $this->args as $key => $value ) {
			$method = $this->utility->buildMethodName( $key, 'set' );
			if( !method_exists( $this, $method ))continue;
			call_user_func([ $this, $method ], $value );
		}
		return $this->args;
	}
}
