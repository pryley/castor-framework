<?php

namespace GeminiLabs\Castor;

use GeminiLabs\Castor\Facades\Development;
use GeminiLabs\Castor\Facades\Template;
use GeminiLabs\Castor\Facades\Theme;
use GeminiLabs\Castor\Facades\Utility;
use WP_Customize_Manager;

class Controller
{
	/**
	 * @return void
	 * @action after_setup_theme
	 */
	public function afterSetupTheme()
	{
		add_editor_style( Theme::assetUri( 'css/editor.css' ));
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form'] );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'soil-clean-up' );
		add_theme_support( 'soil-jquery-cdn' );
		add_theme_support( 'soil-nav-walker' );
		add_theme_support( 'soil-nice-search' );
		add_theme_support( 'soil-relative-urls' );
		add_theme_support( 'title-tag' );
		load_theme_textdomain( 'castor', Theme::paths( 'dir.template' ) . '/languages' );

		$menus = apply_filters( 'castor/register/nav_menus', [
			'main_menu' => __( 'Main Menu', 'castor' ),
		]);

		foreach( $menus as $location => $description ) {
			register_nav_menu( $location, $description );
		}
	}

	/**
	 * @return array
	 * @filter body_class
	 */
	public function filterBodyClasses( array $classes )
	{
		if( Theme::displaySidebar() ) {
			$classes[] = 'has-sidebar';
		}
		return array_keys( array_flip( $classes ));
	}

	/**
	 * @return string
	 * @filter login_headertitle
	 */
	public function filterLoginTitle()
	{
		return get_bloginfo( 'name' );
	}

	/**
	 * @return string
	 * @filter login_headerurl
	 */
	public function filterLoginUrl()
	{
		return get_bloginfo( 'url' );
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

	/**
	 * @return void
	 * @action login_head
	 */
	public function login()
	{
		if( file_exists( Theme::assetPath( 'css/login.css' ))) {
			printf( '<link rel="stylesheet" href="%s">', Theme::assetUri( 'css/login.css' ));
		}
	}

	/**
	 * @return void
	 * @action admin_enqueue_scripts
	 */
	public function registerAdminAssets()
	{
		if( file_exists( Theme::assetPath( 'css/admin.css' ))) {
			wp_enqueue_style( 'castor/admin.css',
				Theme::assetUri( 'css/admin.css' ),
				apply_filters( 'castor/enqueue/admin/css/deps', [] ),
				null
			);
		}
		if( file_exists( Theme::assetPath( 'css/admin.js' ))) {
			wp_enqueue_script( 'castor/admin.js',
				Theme::assetUri( 'js/admin.js' ),
				apply_filters( 'castor/enqueue/admin/js/deps', [] ),
				null,
				true
			);
		}
	}

	/**
	 * @return void
	 * @action wp_enqueue_scripts
	 */
	public function registerAssets()
	{
		wp_enqueue_style( 'castor/main.css',
			Theme::assetUri( 'css/main.css' ),
			apply_filters( 'castor/enqueue/css/deps', [] ),
			null
		);
		wp_enqueue_script( 'castor/main.js',
			Theme::assetUri( 'js/main.js' ),
			apply_filters( 'castor/enqueue/js/deps', [] ),
			null,
			true
		);
		wp_localize_script( 'castor/main.js', apply_filters( 'castor/enqueue/js/localize/variable', 'globals' ),
			apply_filters( 'castor/enqueue/js/localize/variables', [
				'ajax' => admin_url( 'admin-ajax.php' ),
			])
		);
	}

	/**
	 * @return void
	 * @action customize_register
	 */
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

	/**
	 * @return void
	 * @action customize_preview_init
	 */
	public function registerCustomizerAssets()
	{
		wp_enqueue_script( 'castor/customizer.js', Theme::assetUri( 'js/customizer.js' ), ['customize-preview'], null, true );
	}

	/**
	 * @return void
	 * @action widgets_init
	 */
	public function registerSidebars()
	{
		$defaults = apply_filters( 'castor/register/sidebars/defaults', [
			'before_widget' => '<div class="widget %1$s %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4>',
			'after_title'   => '</h4>',
		]);

		$sidebars = apply_filters( 'castor/register/sidebars', [
			'sidebar-primary' => __( 'Primary Sidebar', 'castor' ),
			'sidebar-footer'  => __( 'Footer Widgets', 'castor' ),
		]);

		foreach( $sidebars as $id => $name ) {
			register_sidebar([
				'id'   => $id,
				'name' => $name,
			] + $defaults );
		}
	}
}
