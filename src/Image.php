<?php

namespace GeminiLabs\Castor;

use GeminiLabs\Castor\Helpers\PostMeta;
use GeminiLabs\Castor\Helpers\Utility;

class Image
{
	public $postmeta;
	public $utility;

	public function __construct( PostMeta $postmeta, Utility $utility )
	{
		$this->postmeta = $postmeta;
		$this->utility  = $utility;
	}

	/**
	 * @param int|string $attachment
	 *
	 * @return null|object
	 */
	public function get( $attachment )
	{
		if( !filter_var( $attachment, FILTER_VALIDATE_INT )) {
			$attachment = $this->postmeta->get( $attachment );
		}
		if( !$attachment )return;

		if( $thumbnail = wp_get_attachment_image_src( $attachment, 'thumbnail' )) {
			$medium = $this->normalizeSrc( wp_get_attachment_image_src( $attachment, 'medium' ), $thumbnail );
			$large = $this->normalizeSrc( wp_get_attachment_image_src( $attachment, 'large' ), $medium );

			return (object) [
				'alt'       => wp_strip_all_tags( get_post_meta( $attachment, '_wp_attachment_image_alt', true ), true ),
				'caption'   => wp_get_attachment_caption( $attachment ),
				'copyright' => wp_strip_all_tags( get_post_meta( $attachment, '_copyright', true ), true ),
				'large'     => $large,
				'medium'    => $medium,
				'permalink' => get_attachment_link( $attachment ),
				'thumbnail' => $this->normalizeSrc( $thumbnail ),
			];
		}
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
