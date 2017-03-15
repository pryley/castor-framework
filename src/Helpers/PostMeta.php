<?php

namespace GeminiLabs\Castor\Helpers;

class PostMeta
{
	public function get( $value, array $args = [] )
	{
		$defaults = [
			'ID'       => get_the_ID(),
			'fallback' => '',
			'single'   => true,
			'prefix'   => 'pollux_',
		];

		$args = shortcode_atts( $defaults, $args );

		if( !empty( $value ) && $value[0] == '_' && !empty( $args['prefix'] )) {
			$args['prefix'] = sprintf( '_%s', rtrim( $args['prefix'], '_' ));
		}

		$metaValue = get_post_meta( $args['ID'], $args['prefix'] . $value, $args['single'] );

		return empty( $metaValue )
			? $args['fallback']
			: $metaValue;
	}
}
