<?php

namespace GeminiLabs\Castor\Helpers;

class Development
{
    public $templatePaths = [];

    protected $utility;

    public function __construct(Utility $utility)
    {
        $this->utility = $utility;
    }

    public function capture()
    {
        ob_start();
        call_user_func_array([$this, 'printF'], func_get_args());
        return ob_get_clean();
    }

    public function className($override = 'dev')
    {
        return $this->isDev() ? $override : '';
    }

    public function debug()
    {
        call_user_func_array([$this, 'printF'], func_get_args());
    }

    public function isDev()
    {
        return defined('DEV') && (bool) DEV && WP_ENV == 'development';
    }

    public function isProduction()
    {
        return WP_ENV == 'production';
    }

    public function printFiltersFor($hook = '')
    {
        global $wp_filter;
        if (empty($hook) || !isset($wp_filter[$hook])) {
            return;
        }
        $this->printF($wp_filter[$hook]);
    }

    public function printTemplatePaths()
    {
        $this->printF(implode("\n", $this->templatePaths()));
    }

    public function storeTemplatePath($template)
    {
        if (is_string($template)) {
            $this->templatePaths[] = $this->utility->trimLeft($template, trailingslashit(WP_CONTENT_DIR));
        }
    }

    protected function printF()
    {
        $args = func_num_args();

        if (1 == $args) {
            printf('<div class="print__r"><pre>%s</pre></div>',
                htmlspecialchars(print_r(func_get_arg(0), true), ENT_QUOTES, 'UTF-8')
            );
        } elseif ($args > 1) {
            echo '<div class="print__r_group">';
            foreach (func_get_args() as $value) {
                $this->printF($value);
            }
            echo '</div>';
        }
    }

    public function templatePaths()
    {
        return $this->templatePaths;
    }
}
