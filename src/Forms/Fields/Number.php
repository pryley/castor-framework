<?php

namespace GeminiLabs\Castor\Forms\Fields;

class Number extends Text
{
    /**
     * @return string
     */
    public function render()
    {
        return parent::render([
            'class' => 'small-text',
            'min' => '0',
            'type' => 'number',
        ]);
    }
}
