<?php

namespace GeminiLabs\Castor\Helpers;

use GeminiLabs\Castor\Helpers\PostMeta;

class Theme
{
	public $postmeta;

	public function __construct( PostMeta $postmeta )
	{
		$this->postmeta = $postmeta;
	}

	/**
	 * @param string $asset
	 *
	 * @return string
	 */
	public function assetPath( $asset )
	{
		return $this->paths( 'dir.stylesheet' ) . 'assets/' . $asset;
	}

	/**
	 * @param string $asset
	 *
	 * @return string
	 */
	public function assetUri( $asset )
	{
		return $this->paths( 'uri.stylesheet' ) . 'assets/' . $asset;
	}

	/**
	 * @return bool
	 */
	public function displaySidebar()
	{
		$conditions = [
			is_archive(),
			is_home(),
			is_single(),
		];

		$display = in_array( true, $conditions );

		return apply_filters( 'castor/display/sidebar', $display );
	}

	/**
	 * @param string $asset
	 *
	 * @return string
	 */
	public function imagePath( $asset )
	{
		return $this->paths( 'dir.stylesheet' ) . 'assets/img/' . $asset;
	}

	/**
	 * @param string $asset
	 *
	 * @return string
	 */
	public function imageUri( $asset )
	{
		return $this->paths( 'uri.stylesheet' ) . 'assets/img/' . $asset;
	}

	public function pageTitle()
	{
		foreach( ['is_404', 'is_archive', 'is_home', 'is_page', 'is_search'] as $bool ) {
			if( !$bool() )continue;
			$method = sprintf( 'get%sTitle', ucfirst( str_replace( 'is_', '', $bool )));
			return $this->$method();
		}

		return get_the_title();
	}

	/**
	 * @param null|string $path
	 *
	 * @return array|string
	 */
	public function paths( $path = null )
	{
		$paths = [
			'dir.stylesheet' => get_stylesheet_directory(),
			'dir.template'   => get_template_directory(),
			'dir.upload'     => wp_upload_dir()['basedir'],
			'uri.stylesheet' => get_stylesheet_directory_uri(),
			'uri.template'   => get_template_directory_uri(),
		];

		if( is_null( $path )) {
			return $paths;
		}

		return array_key_exists( $path, $paths )
			? trailingslashit( $paths[$path] )
			: '';
	}

	/**
	 * @param null|string $path
	 *
	 * @return string|null
	 */
	public function svg( $path = null )
	{
		if( $svg = file_get_contents( $this->imageUri( $path ))) {
			return $svg;
		}
	}

	protected function get404Title()
	{
		return __( 'Not Found', 'castor' );
	}

	protected function getArchiveTitle()
	{
		return get_the_archive_title();
	}

	protected function getHomeTitle()
	{
		return ( $home = get_option( 'page_for_posts', true ))
			? get_the_title( $home )
			: __( 'Latest Posts', 'castor' );
	}

	protected function getPageTitle()
	{
		return ($title = $this->postmeta->get( 'title' ))
			? $title
			: get_the_title();
	}

	protected function getSearchTitle()
	{
		return sprintf( __( 'Search Results for %s', 'castor' ), get_search_query() );
	}
}
