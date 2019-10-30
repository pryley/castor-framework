<?php

namespace GeminiLabs\Castor\Forms\Fields;

class Hidden extends Text
{
    /**
     * @return string
     */
    public function render()
    {
        if (isset($this->args['label'])) {
            unset($this->args['label']);
        }

        if (isset($this->args['desc'])) {
            unset($this->args['desc']);
        }

        if (isset($this->args['id'])) {
            unset($this->args['id']);
        }

        return parent::render([
            'class' => '',
            'type' => 'hidden',
        ]);
    }
}
