<?php

namespace GeminiLabs\Castor;

use GeminiLabs\Castor\Helpers\Utility;

class Image
{
	public $utility;

	public function __construct( Utility $utility )
	{
		$this->utility = $utility;
	}

	/**
	 * @param int $attachmentId
	 *
	 * @return null|object
	 */
	public function get( $attachmentId )
	{
		$thumbnail = wp_get_attachment_image_src( $attachmentId, 'thumbnail' );

		if( !$thumbnail )return;

		$medium = $this->normalizeSrc( wp_get_attachment_image_src( $attachmentId, 'medium' ), $thumbnail );
		$large = $this->normalizeSrc( wp_get_attachment_image_src( $attachmentId, 'large' ), $medium );

		return (object) [
			'alt'       => wp_strip_all_tags( get_post_meta( $attachmentId, '_wp_attachment_image_alt', true ), true ),
			'caption'   => wp_get_attachment_caption( $attachmentId ),
			'copyright' => wp_strip_all_tags( get_post_meta( $attachmentId, '_copyright', true ), true ),
			'large'     => $large,
			'medium'    => $medium,
			'permalink' => get_attachment_link( $attachmentId ),
			'thumbnail' => $this->normalizeSrc( $thumbnail ),
		];
	}

	/**
	 * @param mixed $fallback
	 *
	 * @return array
	 */
	protected function normalizeSrc( array $image, $fallback = false )
	{
		if( is_array( $fallback ) && count( array_diff( $image, $fallback )) < 2 ) {
			$image = $fallback;
		}
		return [
			'url'    => $image[0],
			'width'  => $image[1],
			'height' => $image[2],
		];
	}
}
