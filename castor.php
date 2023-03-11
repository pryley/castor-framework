<?php

defined('WPINC') || die;

global $wp_version;

if (!is_admin() && version_compare('7.1', phpversion(), '>')) {
    wp_die(
        __('You must be using PHP 7.1.0 or greater.', 'castor'),
        __('Unsupported PHP version', 'castor')
    );
}
if (!is_admin() && version_compare('5.2', $wp_version, '>')) {
    wp_die(
        __('You must be using WordPress 5.2.0 or greater.', 'castor'),
        __('Unsupported WordPress version', 'castor')
    );
}
if (is_customize_preview() && filter_input(INPUT_GET, 'theme')) {
    wp_die(
        __('Theme must be activated prior to using the customizer.', 'castor')
    );
}

define('CASTOR_FRAMEWORK_VERSION', '1.4.3');

if (version_compare($wp_version, '5.3', '<')) {
    require_once ABSPATH.'/'.WPINC.'/class-oembed.php';
} else {
    require_once ABSPATH.'/'.WPINC.'/class-wp-oembed.php';
}

if (!function_exists('castor_app')) {
    function castor_app()
    {
        return \GeminiLabs\Castor\Application::getInstance();
    }
}

\GeminiLabs\Castor\Application::getInstance()->init();
