<?php

namespace GeminiLabs\Castor;

use GeminiLabs\Castor\Helpers\PostMeta;
use GeminiLabs\Castor\Helpers\Utility;

class Image
{
	public $image;

	protected $postmeta;
	protected $utility;

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
		if( !( $attachment = $this->normalize( $attachment )))return;
		if( $thumbnail = wp_get_attachment_image_src( $attachment, 'thumbnail' )) {
			$medium = $this->normalizeSrc( wp_get_attachment_image_src( $attachment, 'medium' ), $thumbnail );
			$large = $this->normalizeSrc( wp_get_attachment_image_src( $attachment, 'large' ), $medium );

			$this->image = (object) [
				'alt'       => wp_strip_all_tags( get_post_meta( $attachment, '_wp_attachment_image_alt', true ), true ),
				'caption'   => wp_get_attachment_caption( $attachment ),
				'copyright' => wp_strip_all_tags( get_post_meta( $attachment, '_copyright', true ), true ),
				'ID'        => $attachment,
				'large'     => $large,
				'medium'    => $medium,
				'permalink' => get_attachment_link( $attachment ),
				'thumbnail' => $this->normalizeSrc( $thumbnail ),
			];
		}
		return $this;
	}

	public function render( $size = 'large' )
	{
		return wp_get_attachment_image( $this->image->ID, $size );
	}

	protected function normalize( $attachmentId )
	{
		if( !filter_var( $attachmentId, FILTER_VALIDATE_INT )) {
			$attachmentId = $this->postmeta->get( $attachmentId );
		}

		$attachment = get_post( $attachmentId );

		if( !$attachmentId || !$attachment || $attachment->post_type != 'attachment' )return;

		return $attachment->ID;
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
