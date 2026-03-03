<?php

defined('WPINC') || exit;

global $wp_version;

if (!is_admin() && version_compare('7.4', phpversion(), '>')) {
    wp_die(
        __('You must be using PHP 7.4 or greater.', 'castor'),
        __('Unsupported PHP version', 'castor')
    );
}
if (!is_admin() && version_compare('6.1', $wp_version, '>')) {
    wp_die(
        __('You must be using WordPress 6.1 or greater.', 'castor'),
        __('Unsupported WordPress version', 'castor')
    );
}
if (is_customize_preview() && filter_input(INPUT_GET, 'theme')) {
    wp_die(
        __('Theme must be activated prior to using the customizer.', 'castor')
    );
}

if (!defined('CASTOR_FRAMEWORK_VERSION')) {
    define('CASTOR_FRAMEWORK_VERSION', '1.6.0');
}
if (!defined('CASTOR_ASSET_VERSION')) {
    define('CASTOR_ASSET_VERSION', CASTOR_FRAMEWORK_VERSION);
}

require_once ABSPATH.'/'.WPINC.'/class-wp-oembed.php';

if (!function_exists('castor_app')) {
    function castor_app()
    {
        return GeminiLabs\Castor\Application::getInstance();
    }
}

GeminiLabs\Castor\Application::getInstance()->init();
