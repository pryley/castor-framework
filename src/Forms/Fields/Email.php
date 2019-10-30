<?php

namespace GeminiLabs\Castor\Forms\Fields;

class Email extends Text
{
    /**
     * @return string
     */
    public function render()
    {
        return parent::render([
            'class' => 'regular-text ltr',
            'type' => 'email',
        ]);
    }
}
