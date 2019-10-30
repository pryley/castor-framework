<?php

namespace GeminiLabs\Castor\Forms\Fields;

class Radio extends Base
{
    protected $multi = true;
    protected $element = 'input';

    /**
     * @return string
     */
    public function render($default = null)
    {
        $inline = $this->args['inline'] ? ' class="inline"' : '';

        return sprintf('<ul%s>%s</ul>%s',
            $inline,
            $this->implodeOptions('multi_input', $default),
            $this->generateDescription()
        );
    }
}
