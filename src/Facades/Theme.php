<?php

namespace GeminiLabs\Castor\Facades;

use GeminiLabs\Castor\Facade;

class Theme extends Facade
{
    /**
     * Get the fully qualified class name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \GeminiLabs\Castor\Helpers\Theme::class;
    }
}
