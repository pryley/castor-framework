<?php

namespace GeminiLabs\Castor\Helpers;

class Theme
{
    public $archiveMeta;
    public $postMeta;

    public function __construct(ArchiveMeta $archiveMeta, PostMeta $postMeta)
    {
        $this->archiveMeta = $archiveMeta;
        $this->postMeta = $postMeta;
    }

    /**
     * @param string $asset
     *
     * @return string
     */
    public function assetPath($asset)
    {
        return $this->paths('dir.stylesheet').'assets/'.$asset;
    }

    /**
     * @param string $asset
     *
     * @return string
     */
    public function assetUri($asset)
    {
        return $this->paths('uri.stylesheet').'assets/'.$asset;
    }

    /**
     * @return string
     */
    public function copyright()
    {
        return __('Copyright', 'castor').' &copy; '.date('Y').', '.get_bloginfo('name');
    }

    /**
     * @return bool
     */
    public function displaySidebar()
    {
        $conditions = [
            is_archive(),
            is_home(),
            is_single(),
        ];

        $display = in_array(true, $conditions);

        return apply_filters('castor/display/sidebar', $display);
    }

    /**
     * @param string $asset
     *
     * @return string
     */
    public function imagePath($asset)
    {
        return $this->assetPath(castor_app()->imgDir.$asset);
    }

    /**
     * @param string $asset
     *
     * @return string
     */
    public function imageUri($asset)
    {
        return $this->assetUri(castor_app()->imgDir.$asset);
    }

    public function pageTitle()
    {
        foreach (['is_404', 'is_archive', 'is_home', 'is_page', 'is_search'] as $bool) {
            if (!$bool()) {
                continue;
            }
            $method = sprintf('get%sTitle', ucfirst(str_replace('is_', '', $bool)));
            return $this->$method();
        }

        return get_the_title();
    }

    /**
     * @param string|null $path
     *
     * @return array|string
     */
    public function paths($path = null)
    {
        $paths = [
            'dir.stylesheet' => get_stylesheet_directory(),
            'dir.template' => get_template_directory(),
            'dir.upload' => wp_upload_dir()['basedir'],
            'uri.stylesheet' => get_stylesheet_directory_uri(),
            'uri.template' => get_template_directory_uri(),
        ];

        if (is_null($path)) {
            return $paths;
        }

        return array_key_exists($path, $paths)
            ? trailingslashit($paths[$path])
            : '';
    }

    /**
     * @param string|null $path
     *
     * @return string|null
     */
    public function svg($path = null)
    {
        if (file_exists($this->imagePath($path))) {
            return file_get_contents($this->imagePath($path));
        }
    }

    protected function get404Title()
    {
        return __('Not Found', 'castor');
    }

    protected function getArchiveTitle()
    {
        return $this->archiveMeta->get('title', get_the_archive_title(), get_query_var('post_type'));
    }

    protected function getHomeTitle()
    {
        return ($home = (string) get_option('page_for_posts'))
            ? get_the_title($home)
            : get_the_archive_title();
    }

    protected function getPageTitle()
    {
        return $this->postMeta->get('title', [
            'fallback' => get_the_title(),
        ]);
    }

    protected function getSearchTitle()
    {
        return sprintf(__('Search Results for %s', 'castor'), get_search_query());
    }
}
