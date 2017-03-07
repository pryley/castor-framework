<?php

namespace GeminiLabs\Castor\Helpers;

class Utility
{
	/**
	 * @param string $suffix
	 * @param string $string
	 * @param bool   $unique
	 *
	 * @return string
	 */
	public function endWith( $suffix, $string, $unique = true )
	{
		return $unique && $this->endsWith( $suffix, $string )
			? $string
			: $string . $suffix;
	}

	/**
	 * @param string $needle
	 * @param string $haystack
	 *
	 * @return string
	 */
	public function endsWith( $needle, $haystack )
	{
		$length = strlen( $needle );
		return $length != 0
			? substr( $haystack, -$length ) === $needle
			: true;
	}

	/**
	 * @param string $prefix
	 * @param string $string
	 * @param bool   $unique
	 *
	 * @return string
	 */
	public function startWith( $prefix, $string, $unique = true )
	{
		return $unique && $this->startsWith( $prefix, $string )
			? $string
			: $prefix . $string;
	}

	/**
	 * @param string $needle
	 * @param string $haystack
	 *
	 * @return string
	 */
	public function startsWith( $needle, $haystack )
	{
		return substr( $haystack, 0, strlen( $needle )) === $needle;
	}

	/**
	 * @param string $string
	 * @param string $needle
	 * @param bool   $caseSensitive
	 *
	 * @return string
	 */
	public function trimLeft( $string, $needle, $caseSensitive = true )
	{
		$strPos = $caseSensitive ? "strpos" : "stripos";
		if( $strPos( $string, $needle ) === 0 ) {
			$string = substr( $string, strlen( $needle ));
		}
		return $string;
	}

	/**
	 * @param string $string
	 * @param string $needle
	 * @param bool   $caseSensitive
	 *
	 * @return string
	 */
	public function trimRight( $string, $needle, $caseSensitive = true )
	{
		$strPos = $caseSensitive ? "strpos" : "stripos";
		if( $strPos( $string, $needle, strlen( $string ) - strlen( $needle )) !== false ) {
			$string = substr( $string, 0, -strlen( $needle ));
		}
		return $string;
	}
}
