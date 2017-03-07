<?php

namespace GeminiLabs\Castor;

use GeminiLabs\Castor\Facades\Development;
use GeminiLabs\Castor\Facades\Template;
use GeminiLabs\Castor\Facades\Theme;
use GeminiLabs\Castor\Facades\Utility;
use WP_Customize_Manager;

class Controller
{
	public function afterSetupTheme()
	{
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form'] );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'soil-clean-up' );
		add_theme_support( 'soil-jquery-cdn' );
		add_theme_support( 'soil-nav-walker' );
		add_theme_support( 'soil-nice-search' );
		add_theme_support( 'soil-relative-urls' );
		add_theme_support( 'title-tag' );
		add_editor_style( Theme::assetUri( 'css/editor.css' ));
		load_theme_textdomain( 'castor', Theme::paths( 'dir.template' ) . '/languages' );
		register_nav_menu( 'main_menu', __( 'Main Menu', 'castor' ));
	}

	/**
	 * @return string
	 * @filter template_include
	 */
	public function filterTemplate( $template )
	{
		if( is_string( $template )) {
			$template = Template::setLayout( $template );
			Development::storeTemplatePath( $template );
		}
		return $template;
	}

	/**
	 * @return array
	 * @filter {$type}_template_hierarchy
	 */
	public function filterTemplateHierarchy( array $templates )
	{
		return array_map( function( $template ) {
			return Utility::startWith( 'templates/', $template );
		}, $templates );
	}

	public function registerAssets()
	{
		wp_enqueue_style( 'castor/main.css', Theme::assetUri( 'css/main.css' ), [], null );
		wp_enqueue_script( 'castor/main.js', Theme::assetUri( 'js/main.js' ), [], null, true );
	}

	public function registerCustomizer( WP_Customize_Manager $manager )
	{
		$manager->get_setting( 'blogname' )->transport = 'postMessage';
		$manager->selective_refresh->add_partial( 'blogname', [
			'selector'        => '.brand',
			'render_callback' => function() {
				bloginfo( 'name' );
			},
		]);
	}

	public function registerCustomizerAssets()
	{
		wp_enqueue_script( 'castor/customizer.js', Theme::assetUri( 'js/customizer.js' ), ['customize-preview'], null, true );
	}

	public function registerSidebars()
	{
		$defaults = [
			'before_widget' => '<section class="widget %1$s %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4>',
			'after_title'   => '</h4>',
		];

		register_sidebar([
			'id'   => 'sidebar-primary',
			'name' => __( 'Primary Sidebar', 'castor' ),
		] + $defaults );

		register_sidebar([
			'id'   => 'sidebar-footer',
			'name' => __( 'Footer Sidebar', 'castor' ),
		] + $defaults );
	}
}
