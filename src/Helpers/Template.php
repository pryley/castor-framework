<?php

namespace GeminiLabs\Castor\Helpers;

use GeminiLabs\Castor\Helpers\Utility;

class Template
{
	public $util;

	public function __construct( Utility $util )
	{
		$this->util = $util;
	}

	/**
	 * @var string
	 */
	public $template;

	/**
	 * @param string $slug
	 * @param string $name
	 *
	 * @return string
	 */
	public function get( $slug, $name = '' )
	{
		$template  = $this->util->startWith( 'templates/', $slug );
		$templates = ["{$template}.php"];

		if( 'index' != basename( $this->template, '.php' )) {
			array_unshift( $templates, "{$template}-{$name}.php" );
		}

		$templates = apply_filters( "castor/templates/{$slug}", $templates, $name );

		return locate_template( $templates );
	}

	/**
	 * @param string $slug
	 * @param string $name
	 *
	 * @return void
	 */
	public function load( $slug, $name = '' )
	{
		$template = $this->get( $slug, $name );
		if( !empty( $template )) {
			load_template( $template, false );
		}
	}

	/**
	 * @return void
	 */
	public function main()
	{
		$this->load( $this->template );
	}

	/**
	 * @param string $template
	 *
	 * @return string|void
	 */
	public function setLayout( $template )
	{
		$this->template = $this->util->trimRight( strstr( $template, 'templates/' ), '.php' );
		return $this->get( apply_filters( 'castor/templates/layout', 'layouts/default' ));
	}
}
