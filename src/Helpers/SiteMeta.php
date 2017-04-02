<?php

namespace GeminiLabs\Castor\Helpers;

class SiteMeta
{
	/**
	 * @param string       $group
	 * @param false|string $key
	 * @param mixed        $fallback
	 *
	 * @return mixed
	 */
	public function get( $group, $key = false, $fallback = '' )
	{
		$metaKey = sprintf( '%ssettings-%s', apply_filters( 'castor/sitemeta/prefix', 'pollux_' ), $group );
		$options = get_option( $metaKey, false );

		if( !$options || !is_array( $options )) {
			return $fallback;
		}

		return is_string( $key )
			? $this->normalize( $options, $key, $fallback )
			: $options;
	}

	/**
	 * @param string $key
	 * @param mixed  $fallback
	 *
	 * @return mixed
	 */
	protected function normalize( array $options, $key, $fallback )
	{
		if( !array_key_exists( $key, $options )) {
			return $fallback;
		}

		$option = $options[$key];

		$option = is_array( $option )
			? array_filter( $option )
			: trim( $option );

		return empty( $option )
			? $fallback
			: $option;
	}
}
