<?php

namespace GeminiLabs\Castor\Helpers;

use GeminiLabs\Castor\Helpers\Utility;

class Development
{
	public $templatePaths = [];

	protected $utility;

	public function __construct( Utility $utility )
	{
		$this->utility  = $utility;
	}

	public function capture()
	{
		ob_start();
		call_user_func_array( [$this, 'printF'], func_get_args() );
		return ob_get_clean();
	}

	public function className()
	{
		return $this->isDev() && in_array( DEV, ['css', true] )
			? 'dev'
			: '';
	}

	public function debug()
	{
		call_user_func_array( [$this, 'printF'], func_get_args() );
	}

	public function isDev()
	{
		return defined( 'DEV' ) && !!DEV && WP_ENV == 'development';
	}

	public function isProduction()
	{
		return WP_ENV == 'production';
	}

	public function printFiltersFor( $hook = '' )
	{
		global $wp_filter;
		if( empty( $hook ) || !isset( $wp_filter[$hook] ))return;
		$this->printF( $wp_filter[ $hook ] );
	}

	public function printTemplatePaths()
	{
		if( $this->isDev() && ( DEV == 'templates' || DEV === true )) {
			$templates = array_map( function( $key, $value ) {
				return sprintf( '[%s] => %s', $key, $value );
			}, array_keys( $this->templatePaths ), $this->templatePaths );
			$this->printF( implode( "\n", $templates ));
		}
	}

	public function storeTemplatePath( $template )
	{
		if( is_string( $template )) {
			$this->templatePaths[] = $this->utility->trimLeft( $template, trailingslashit( WP_CONTENT_DIR ));
		}
	}

	protected function printF()
	{
		$args = func_num_args();

		if( $args == 1 ) {
			printf( '<div class="print__r"><pre>%s</pre></div>',
				htmlspecialchars( print_r( func_get_arg(0), true ), ENT_QUOTES, 'UTF-8' )
			);
		}
		else if( $args > 1 ) {
			echo '<div class="print__r_group">';
			foreach( func_get_args() as $value ) {
				$this->printF( $value );
			}
			echo '</div>';
		}
	}
}
