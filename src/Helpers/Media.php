<?php

namespace GeminiLabs\Castor\Helpers;

use GeminiLabs\Castor\Gallery;
use GeminiLabs\Castor\Image;
use GeminiLabs\Castor\Video;
use BadMethodCallException;

class Media
{
	protected $gallery;
	protected $image;
	protected $video;

	public function __construct( Gallery $gallery, Image $image, Video $video )
	{
		$this->gallery = $gallery;
		$this->image   = $image;
		$this->video   = $video;
	}

	/**
	 * @param string $method
	 *
	 * @return string|void
	 * @throws BadMethodCallException
	 */
	public function __call( $method, array $args )
	{
		if( !$this->verifyClassProperty( $method )) {
			throw new BadMethodCallException( sprintf( 'Not a valid method: %s', $method ));
		}
		if( !count( $args )) {
			throw new BadMethodCallException( sprintf( 'Missing arguments for: %s', $method ));
		}
		isset( $args[1] ) || $args[1] = '';
		return $this->$method->get( $args[0] )->render( $args[1] );
	}

	/**
	 * @param string $mediaType
	 *
	 * @return mixed
	 */
	public function get( $mediaType, array $args = [] )
	{
		if( $this->verifyClassProperty( $mediaType )) {
			return $this->$mediaType->get( $args )->$mediaType;
		}
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	protected function verifyClassProperty( $name )
	{
		return property_exists( $this, $name ) && is_object( $this->$name );
	}
}
