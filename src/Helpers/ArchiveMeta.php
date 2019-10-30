<?php

namespace GeminiLabs\Castor\Helpers;

/**
 * ArchiveMeta::all();
 * ArchiveMeta::group();
 * ArchiveMeta::group('option','fallback');
 * ArchiveMeta::get('group');
 * ArchiveMeta::get('group','option','fallback');.
 *
 * @property object all
 */
class ArchiveMeta extends SiteMeta
{
    protected $options;

    public function __construct()
    {
        $option = apply_filters('pollux/archives/id', 'pollux_archives');
        $this->options = (array) get_option($option, []);
    }

    /**
     * @param string|null $key
     * @param mixed $fallback
     * @param string $group
     * @return mixed
     */
    public function get($key = '', $fallback = null, $group = '')
    {
        return parent::get($group, $key, $fallback);
    }

    /**
     * @return string
     */
    protected function getDefaultGroup()
    {
        return (string) get_post_type();
    }
}
