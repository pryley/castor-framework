<?php

namespace GeminiLabs\Castor\Forms\Fields;

class Select extends Base
{
    protected $element = 'select';

    /**
     * @return string
     */
    public function render(array $defaults = [])
    {
        $defaults = wp_parse_args($defaults, [
            'type' => 'select',
        ]);

        return sprintf('<select %s>%s</select>%s',
            $this->implodeAttributes($defaults),
            $this->implodeOptions('select_option'),
            $this->generateDescription()
        );
    }
}
