<?php

namespace GeminiLabs\Castor\Forms\Fields;

use GeminiLabs\Castor\Forms\Fields\Base;

class Text extends Base
{
	protected $element = 'input';

	/**
	 * @return string
	 */
	public function render( array $defaults = [] )
	{
		$defaults = wp_parse_args( $defaults, [
			'class' => 'regular-text',
			'type'  => 'text',
		]);

		return sprintf( '<input %s/>%s',
			$this->implodeAttributes( $defaults ),
			$this->generateDescription()
		);
	}
}
