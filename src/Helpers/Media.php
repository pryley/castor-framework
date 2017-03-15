<?php

namespace GeminiLabs\Castor\Helpers;

use GeminiLabs\Castor\Gallery;
use GeminiLabs\Castor\Image;
use GeminiLabs\Castor\Video;
use BadMethodCallException;

/**
 * @method string      gallery( array $args )
 * @method \WP_Query   getGallery( array $args )
 * @method object|void getImage( int|string $attachment )
 * @method object|void getVideo( string|array $args )
 * @method string|void image( int|string $attachment, string|array $size )
 * @method string|void video( string|array $args )
 */
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

		if( !count( $args )) {
			throw new BadMethodCallException( sprintf( 'Missing arguments for: %s', $name ));
		}
		if( str_replace( $mediaType, '', strtolower( $name ))) {
			return $this->$mediaType->get( $args[0] )->$mediaType;
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
	 * @throws BadMethodCallException
	 */
	public function get( $name, $args = [] )
	{
		$mediaType = $this->validateMethod( $name );
		return $this->$mediaType->get( $args )->$mediaType;
	}

	/**
	 * @param string $name
	 *
	 * @return string|false
	 * @throws BadMethodCallException
	 */
	protected function validateMethod( $name )
	{
		foreach( [$name, strtolower( substr( $name, 3 ))] as $method ) {
			if( property_exists( $this, $method ) && is_object( $this->$method )) {
				return $method;
			}
		}
		throw new BadMethodCallException( sprintf( 'Not a valid method: %s', $name ));
	}
}
