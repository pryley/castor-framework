<?php

namespace GeminiLabs\Castor\Forms\Fields;

class Checkbox extends Base
{
    protected $element = 'input';

    public function __construct(array $args = [])
    {
        parent::__construct($args);

        if (count($args['options']) > 1) {
            $this->multi = true;
        }
    }

    /**
     * @return string
     */
    public function render()
    {
        $inline = $this->args['inline'] ? ' class="inline"' : '';

        if ($this->multi) {
            return sprintf('<ul%s>%s</ul>%s',
                $inline,
                $this->implodeOptions('multi_input_checkbox'),
                $this->generateDescription()
            );
        }

        return sprintf('%s%s',
            $this->implodeOptions('single_input'),
            $this->generateDescription()
        );
    }
}
