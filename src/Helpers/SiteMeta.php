<?php

namespace GeminiLabs\Castor\Helpers;

/**
 * SiteMeta::all();
 * SiteMeta::group();
 * SiteMeta::group('option','fallback');
 * SiteMeta::get('group');
 * SiteMeta::get('group','option','fallback');.
 *
 * @property object all
 */
class SiteMeta
{
    protected $options;

    public function __construct()
    {
        $this->options = get_option(apply_filters('pollux/settings/id', 'pollux_settings'), []);
    }

    /**
     * @param string $group
     * @return object|array|null
     */
    public function __call($group, $args)
    {
        $args = array_pad($args, 2, null);
        $group = $this->$group;
        if (is_object($group)) {
            return $group;
        }
        return $this->get($group, $args[0], $args[1]);
    }

    /**
     * @param string $group
     * @return object|array|null
     */
    public function __get($group)
    {
        if ('all' == $group) {
            return (object) $this->options;
        }
        if (empty($group)) {
            $group = $this->getDefaultGroup();
        }
        if (is_array($group)) {
            $group = reset($group);
        }
        return isset($this->options[$group])
            ? $this->options[$group]
            : null;
    }

    /**
     * @param string $group
     * @param string|null $key
     * @param mixed $fallback
     * @return mixed
     */
    public function get($group = '', $key = '', $fallback = null)
    {
        if (func_num_args() < 1) {
            return $this->all;
        }
        if (is_string($group)) {
            $group = $this->$group;
        }
        if (!is_array($group)) {
            return $fallback;
        }
        if (is_null($key)) {
            return $group;
        }
        return $this->getValue($group, $key, $fallback);
    }

    /**
     * @return string
     */
    protected function getDefaultGroup()
    {
        return '';
    }

    /**
     * @param string $key
     * @param mixed $fallback
     * @return mixed
     */
    protected function getValue(array $group, $key = '', $fallback = null)
    {
        if (empty($key) || !array_key_exists($key, $group)) {
            return $fallback;
        }
        return empty($group[$key]) && !is_null($fallback)
            ? $fallback
            : $group[$key];
    }
}
