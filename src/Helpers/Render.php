<?php

namespace GeminiLabs\Castor\Helpers;

use GeminiLabs\Castor\Helpers\Media;
use GeminiLabs\Castor\Helpers\PostMeta;
use GeminiLabs\Castor\Helpers\Theme;
use GeminiLabs\Castor\Helpers\Utility;

class Render
{
	public $media;
	public $postmeta;
	public $theme;
	public $utility;

	public function __construct( Media $media, PostMeta $postmeta, Theme $theme, Utility $utility )
	{
		$this->media    = $media;
		$this->postmeta = $postmeta;
		$this->theme    = $theme;
		$this->utility  = $utility;
	}

	public function blockquote( $metaKey = false, array $attributes = [] )
	{
		if( $value = $this->postmeta->get( $metaKey )) {
			$this->utility->printTag( 'blockquote', wp_strip_all_tags( $value ), $attributes );
		}
	}

	public function button( $postId = 0, $title = false )
	{
		$post = get_post( $postId );

		if( !$post )return;
		if( !$title ) {
			$title = $post->post_title;
		}

		printf( '<a href="%s" class="button"><span>%s</span></a>',
			get_permalink( $post->ID ),
			$title
		);
	}

	public function buttons( array $postIds = [] )
	{
		foreach( $postIds as $postId ) {
			$this->button( $postId );
		}
	}

	public function content( $metaKey = false )
	{
		$content = $metaKey
			? $this->postmeta->get( $metaKey )
			: get_the_content();

		$content = apply_filters( 'the_content', $content );

		print str_replace( ']]>', ']]&gt;', $content );
	}

	public function gallery( array $args = [] )
	{
		print $this->media->gallery( $args );
	}

	public function title( $metaKey = false, array $attributes = [] )
	{
		$tag = apply_filters( 'castor/render/title/tag', 'h2' );
		$value = $metaKey
			? $this->postmeta->get( $metaKey )
			: $this->theme->pageTitle();

		if( !$value )return;

		$this->utility->printTag( $tag, wp_strip_all_tags( $value ), $attributes );
	}

	public function video( $metaKey = 'video', $screenshotMetaKey = false )
	{
	}
}
