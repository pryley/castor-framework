<?php

namespace GeminiLabs\Castor;

use GeminiLabs\Castor\AliasLoader;
use GeminiLabs\Castor\Container;
use GeminiLabs\Castor\Facade;

final class Application extends Container
{
	public function __construct()
	{
		Facade::clearResolvedInstances();
		Facade::setFacadeApplication( $this );
		$this->registerAliases();
	}

	public function init()
	{
		$controller = $this->make( 'Controller' );

		// Action hooks
		add_action( 'after_setup_theme',      [$controller, 'afterSetupTheme'], 20 );
		add_action( 'wp_enqueue_scripts',     [$controller, 'registerAssets'] );
		add_action( 'customize_register',     [$controller, 'registerCustomizer'] );
		add_action( 'customize_preview_init', [$controller, 'registerCustomizerAssets'] );
		add_action( 'widgets_init',           [$controller, 'registerSidebars'] );

		// Filter hooks
		add_filter( 'template_include',       [$controller, 'filterTemplate'] );

		foreach( $this->getTemplateTypes() as $type ) {
			add_filter( "{$type}_template_hierarchy", [$controller, 'filterTemplateHierarchy'] );
		}
	}

	/**
	 * @return void
	 */
	public function registerAliases()
	{
		$aliases = [
			'Development' => Facades\Development::class,
			'Template'    => Facades\Template::class,
			'Theme'       => Facades\Theme::class,
			'Utility'     => Facades\Utility::class,
		];

		$aliases = apply_filters( 'castor/register/aliases', $aliases );

		AliasLoader::getInstance( $aliases )->register();
	}

	/**
	 * @return array
	 */
	protected function getTemplateTypes()
	{
		return [
			'404', 'archive', 'attachment', 'author', 'category', 'date',
			'embed', 'frontpage', 'home', 'index', 'page', 'paged',
			'search', 'single', 'singular', 'tag', 'taxonomy',
		];
	}
}
