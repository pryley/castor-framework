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
	 * @param string $name
	 *
	 * @return string|void
	 * @throws BadMethodCallException
	 */
	public function __call( $name, array $args )
	{
		$mediaType = $this->validateMethod( $name );
		if( !$mediaType ) {
			throw new BadMethodCallException( sprintf( 'Not a valid method: %s', $name ));
		}
		if( !count( $args )) {
			throw new BadMethodCallException( sprintf( 'Missing arguments for: %s', $name ));
		}
		return !empty( $args[1] )
			? $this->$mediaType->get( $args[0] )->render( $args[1] )
			: $this->$mediaType->get( $args[0] )->render();
	}

	/**
	 * @param string $name
	 * @param mixed  $args
	 *
	 * @return mixed
	 */
	public function get( $name, $args = [] )
	{
		if( $mediaType = $this->validateMethod( $name )) {
			return $this->$mediaType->get( $args )->$mediaType;
		}
	}

	/**
	 * @param string $name
	 *
	 * @return string|false
	 */
	protected function validateMethod( $name )
	{
		foreach( [$name, strtolower( substr( $name, 3 ))] as $method ) {
			if( in_array( $method, ['gallery', 'image', 'video'] )
				&& property_exists( $this, $method )
				&& is_object( $this->$method )) {
				return $method;
			}
		}
		return false;
	}
}
