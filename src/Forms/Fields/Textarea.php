<?php

namespace GeminiLabs\Castor\Forms\Fields;

class Textarea extends Base
{
    protected $element = 'textarea';

    /**
     * @return string
     */
    public function render(array $defaults = [])
    {
        $defaults = wp_parse_args($defaults, [
            'class' => 'large-text',
            'rows' => 3,
            'type' => 'textarea',
        ]);

        return sprintf('<textarea %s>%s</textarea>%s',
            $this->implodeAttributes($defaults),
            $this->args['value'],
            $this->generateDescription()
        );
    }
}
