<?php

namespace GeminiLabs\Castor\Forms\Fields;

class Url extends Text
{
    /**
     * @return string
     */
    public function render()
    {
        return parent::render([
            'class' => 'regular-text code',
            'type' => 'url',
        ]);
    }
}
