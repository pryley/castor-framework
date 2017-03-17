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

		if( !$postId || !$post )return;
		if( !$title ) {
			$title = $post->post_title;
		}
		printf( '<a href="%s" class="button"><span>%s</span></a>',
			get_permalink( $post->ID ),
			$title
		);
	}

	public function buttons( $postIds = [] )
	{
		foreach( (array) $postIds as $postId ) {
			$this->button( $postId );
		}
	}

	public function content( $metaKey = false )
	{
		$content = $metaKey
			? $this->postmeta->get( $metaKey )
			: get_the_content();

		echo str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $content ));
	}

	public function featured( $args = [] )
	{
		$args = wp_parse_args( $args, [
			'class' => 'featured',
			'image' => get_post_thumbnail_id(),
			'video' => 'featured_video',
		]);
		$featuredHtml = $this->media->video( wp_parse_args( $args, [
			'url' => $args['video'],
		]));
		if( empty( $featuredHtml ) && $featuredImage = $this->media->getImage( $args['image'] )) {
			$featuredHtml = sprintf( '<div class="featured-image"><img src="%s" alt="%s"></div><figcaption>%s</figcaption>',
				$featuredImage->large['url'],
				$featuredImage->alt,
				$featuredImage->caption
			);
		}
		if( !empty( $featuredHtml )) {
			printf( '<figure class="%s">%s</figure>', $args['class'], $featuredHtml );
		}
	}

	public function gallery( array $args = [] )
	{
		echo $this->media->gallery( $args );
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

	/**
	 * @param array|string $args
	 *
	 * @return string|null
	 */
	public function video( $args )
	{
		echo $this->media->video( $args );
	}
}
