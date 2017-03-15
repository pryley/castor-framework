<?php

namespace GeminiLabs\Castor\Helpers;

class PostMeta
{
	public function get( $metaKey, array $args = [] )
	{
		if( empty( $metaKey ))return;

		$args = $this->normalize( $args );
		$metaKey = $this->buildMetaKey( $metaKey, $args['prefix'] );
		$metaValue = get_post_meta( $args['ID'], $metaKey, $args['single'] );

		return empty( $metaValue )
			? $args['fallback']
			: $metaValue;
	}

	protected function buildMetaKey( $metaKey, $prefix )
	{
		return ( substr( $metaKey, 0, 1 ) == '_' && !empty( $prefix ))
			? sprintf( '_%s%s', rtrim( $prefix, '_' ), $metaKey )
			: $prefix . $metaKey;
	}

	protected function normalize( array $args )
	{
		$defaults = [
			'ID'       => get_the_ID(),
			'fallback' => '',
			'single'   => true,
			'prefix'   => apply_filters( 'castor/postmeta/prefix', 'pollux_' ),
		];
		return shortcode_atts( $defaults, $args );
	}
}
