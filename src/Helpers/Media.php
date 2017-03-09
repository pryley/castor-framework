<?php

namespace GeminiLabs\Castor\Helpers;

use GeminiLabs\Castor\Helpers\PostMeta;
use GeminiLabs\Castor\Helpers\Theme;
use WP_Post;
use WP_Query;

class Media
{
	public $postmeta;
	public $theme;

	public function __construct( PostMeta $postmeta, Theme $theme )
	{
		$this->postmeta = $postmeta;
		$this->theme    = $theme;
	}

	/**
	 * @return string
	 */
	public function gallery( array $args = [] )
	{
		$gallery = $this->getGallery( $args );

		return $this->renderGallery( $gallery ) . $this->renderGalleryPagination( $gallery );
	}

	/**
	 * @return WP_Query
	 */
	public function getGallery( array $args )
	{
		$args = $this->normalizeArgs( $args );

		return new WP_Query([
			'orderby'        => 'post__in',
			'paged'          => $this->getPaged(),
			'post__in'       => $this->getMediaIds( $args ),
			'post_mime_type' => 'image',
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => $this->getImagesPerPage( $args ),
		]);
	}

	/**
	 * @return string
	 */
	public function renderGallery( WP_Query $gallery )
	{
		$images = array_reduce( $gallery->posts, function( $images, $attachment ) {
			return $images . $this->renderGalleryImage( $attachment );
		});

		return sprintf( '<div class="gallery-images" itemscope itemtype="http://schema.org/ImageGallery">%s</div>', $images );
	}

	/**
	 * @return null|string
	 */
	public function renderGalleryImage( WP_Post $attachment )
	{
		$image = $this->getImageSrc( $attachment->ID );
		if( !$image )return;
		return sprintf(
			'<figure class="gallery-image" data-w="%s" data-h="%s" data-ps=\'%s\' itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">' .
				'<a href="%s" itemprop="contentUrl"><img src="%s" data-src="%s" itemprop="thumbnail" alt="%s"/></a>' .
				'<figcaption itemprop="caption description">%s <span itemprop="copyrightHolder">%s</span></figcaption>' .
			'</figure>',
			$image->thumbnail['width'],
			$image->thumbnail['height'],
			$this->getPhotoswipeData( $image ),
			get_attachment_link( $attachment->ID ),
			$this->theme->imageUri( 'blank.gif' ),
			$image->thumbnail['url'],
			$image->alt,
			$image->caption,
			$image->copyright
		);
	}

	/**
	 * @return string
	 */
	public function renderGalleryPagination( WP_Query $query )
	{
		return paginate_links([
			'before_page_number' => '<span class="screen-reader-text">' . __( 'Page', 'castor' ) . ' </span>',
			'current'            => $query->query['paged'],
			'mid_size'           => 1,
			'next_text'          => __( 'Next', 'castor' ),
			'prev_text'          => __( 'Previous', 'castor' ),
			'total'              => $query->max_num_pages,
		]);
	}

	/**
	 * @return mixed
	 */
	protected function getImagesPerPage( array $args )
	{
		$args = $this->normalizeArgs( $args );

		return $this->postmeta->get( $args['per_page'], [
			'fallback' => -1,
		]);
	}

	/**
	 * @param int $id
	 *
	 * @return null|object
	 */
	protected function getImageSrc( $id )
	{
		$thumbnail = wp_get_attachment_image_src( $id, 'thumbnail' );

		if( !$thumbnail )return;

		$medium = $this->normalizeImageSrc( wp_get_attachment_image_src( $id, 'medium' ), $thumbnail );
		$large = $this->normalizeImageSrc( wp_get_attachment_image_src( $id, 'large' ), $medium );

		return (object) [
			'alt'       => trim( strip_tags( get_post_meta( $id, '_wp_attachment_image_alt', true ))),
			'caption'   => wp_get_attachment_caption( $id ),
			'copyright' => trim( strip_tags( get_post_meta( $id, '_copyright', true ))),
			'thumbnail' => $this->normalizeImageSrc( $thumbnail ),
			'medium'    => $medium,
			'large'     => $large,
		];
	}

	/**
	 * @return array
	 */
	protected function getMediaIds( array $args )
	{
		$args = $this->normalizeArgs( $args );

		return wp_parse_id_list( $this->postmeta->get( $args['media'], [
			'ID'     => $this->postmeta->get( $args['gallery'] ),
			'single' => false,
		]));
	}

	/**
	 * @return int
	 */
	protected function getPaged()
	{
		return intval( get_query_var(( is_front_page() ? 'page' : 'paged' ))) ?: 1;
	}

	/**
	 * @return string
	 */
	protected function getPhotoswipeData( $image )
	{
		return sprintf( '{"l":{"src":"%s","w":%d,"h":%d},"m":{"src":"%s","w":%d,"h":%d}}',
			$image->large['url'],
			$image->large['width'],
			$image->large['height'],
			$image->medium['url'],
			$image->medium['width'],
			$image->medium['height']
		);
	}

	/**
	 * @return array
	 */
	protected function normalizeArgs( array $args = [] )
	{
		return shortcode_atts([
			'gallery'  => 'gallery',
			'media'    => 'media',
			'per_page' => 'per_page',
		], $args );
	}

	/**
	 * @param mixed $fallback
	 *
	 * @return array
	 */
	protected function normalizeImageSrc( array $image, $fallback = false )
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
