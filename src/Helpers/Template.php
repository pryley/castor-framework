<?php

namespace GeminiLabs\Castor\Helpers;

use GeminiLabs\Castor\Facades\Development;
use GeminiLabs\Castor\Facades\Log;
use GeminiLabs\Castor\Facades\Utility;

class Template
{
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
		$template = Utility::startWith( 'templates/', $slug );
		$templates = ["$template.php"];
		if( !empty( $name )) {
			$fileName = basename( $template );
			$filePath = Utility::trimRight( $template, $fileName );
			array_unshift( $templates, sprintf( '%s/%s.php', $filePath . $name, $fileName ));
		}
		$templates = array_unique( apply_filters( "castor/templates/$slug", $templates, $name ));
		$template = locate_template( $templates );
		if( empty( $template )) {
			if( file_exists( "$slug.php" )) {
				return "$slug.php";
			}
			Log::debug( "$slug not found." );
		}
		return $template;
	}

	/**
	 * @param string $slug
	 * @param string $name
	 *
	 * @return void
	 */
	public function load( $slug, $name = '' )
	{
		if( !empty(( $template = $this->get( $slug, $name )))) {
			Development::storeTemplatePath( $template );
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
		$this->template = Utility::trimRight( $template, '.php' );
		return $this->get( apply_filters( 'castor/templates/layout', 'layouts/default' ));
	}
}
