<?php

namespace GeminiLabs\Castor\Helpers;

class Development
{
	public $templatePaths = [];

	public function capture()
	{
		ob_start();
		call_user_func_array( [$this, 'print'], func_get_args() );
		return ob_get_clean();
	}

	public function className()
	{
		return $this->isDev() && in_array( DEV, ['css', true] )
			? 'dev'
			: '';
	}

	public function debug( $value )
	{
		$this->print( $value );
	}

	public function isDev()
	{
		return defined( 'DEV' ) && !!DEV && WP_ENV == 'development';
	}

	public function isProduction()
	{
		return WP_ENV == 'production';
	}

	public function print( $value )
	{
		$args = func_num_args();

		if( $args == 1 ) {
			printf( '<div class="print__r"><pre>%s</pre></div>',
				htmlspecialchars( print_r( $value, true ), ENT_QUOTES, 'UTF-8' )
			);
		}
		else if( $args > 1 ) {
			echo '<div class="print__r_group">';
			foreach( func_get_args() as $param ) {
				$this->print( $param );
			}
			echo '</div>';
		}
	}

	public function printFiltersFor( $hook = '' )
	{
		global $wp_filter;
		if( empty( $hook ) || !isset( $wp_filter[$hook] ))return;
		$this->print( $wp_filter[ $hook ] );
	}

	public function printTemplatePaths()
	{
		if( $this->isDev() && ( DEV == 'templates' || DEV === true )) {
			$templates = array_keys( array_flip( $this->templatePaths ));
			$templates = array_map( function( $key, $value ) {
				return sprintf( '[%s] => %s', $key, $value );
			}, array_keys( $templates ), $templates );

			$this->print( implode( "\n", $templates ));
		}
	}

	public function storeTemplatePath( $template )
	{
		if( is_string( $template )) {
			$themeName = basename( strstr( $template, '/templates/', true ));
			$this->templatePaths[] = $themeName . strstr( $template, '/templates/' );
		}
	}
}
