<?php

namespace GeminiLabs\Castor\Forms\Fields;

class Submit extends Text
{
    /**
     * @return string
     */
    public function render()
    {
        if (isset($this->args['name'])) {
            $this->args['name'] = 'submit';
        }

        return parent::render([
            'class' => 'button button-primary',
            'type' => 'submit',
        ]);
    }
}
