<?php

namespace GeminiLabs\Castor\Helpers;

use GeminiLabs\Castor\Gallery;
use GeminiLabs\Castor\Image;
use GeminiLabs\Castor\Video;

class Media
{
	public $gallery;
	public $image;
	public $video;

	public function __construct( Gallery $gallery, Image $image, Video $video )
	{
		$this->gallery = $gallery;
		$this->image   = $image;
		$this->video   = $video;
	}

	/**
	 * @return void|string
	 */
	public function gallery( array $args = [] )
	{
		$gallery = $this->gallery->query( $args );

		if( $gallery->have_posts() ) {
			return $this->gallery->render( $gallery ) . $this->gallery->renderPagination( $gallery );
		}
	}

	/**
	 * @return WP_Query
	 */
	public function getGalleryQuery( array $args = [] )
	{
		return $this->gallery->query( $args );
	}

	/**
	 * @return void|string
	 */
	public function video( $args = [] )
	{
		if( is_string( $args )) {
			$args = ['url' => $args];
		}
		return $this->video->get( $args )->render();
	}
}
