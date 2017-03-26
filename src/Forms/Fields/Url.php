<?php

namespace GeminiLabs\Castor\Forms\Fields;

use GeminiLabs\Castor\Forms\Fields\Text;

class Url extends Text
{
	/**
	 * @return string
	 */
	public function render()
	{
		return parent::render([
			'class' => 'regular-text code',
			'type'  => 'url',
		]);
	}
}
