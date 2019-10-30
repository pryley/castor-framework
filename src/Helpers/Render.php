<?php

namespace GeminiLabs\Castor\Helpers;

class Render
{
    public $media;
    public $postmeta;
    public $theme;
    public $utility;

    public function __construct(Media $media, PostMeta $postmeta, Theme $theme, Utility $utility)
    {
        $this->media = $media;
        $this->postmeta = $postmeta;
        $this->theme = $theme;
        $this->utility = $utility;
    }

    public function blockquote($metaKey = false, array $attributes = [])
    {
        if ($value = $this->postmeta->get($metaKey)) {
            $this->utility->printTag('blockquote', wp_strip_all_tags($value), $attributes);
        }
    }

    public function button($postId = 0, $title = false, $passthrough = false)
    {
        if ($passthrough) {
            $url = $postId;
        }
        if (!isset($url)) {
            $url = get_permalink($postId);
            if (!$title) {
                $title = get_the_title($postId);
            }
        }
        if (!$title || !$url) {
            return;
        }
        printf('<a href="%s" class="button"><span>%s</span></a>', $url, $title);
    }

    public function buttons($postIds = [])
    {
        foreach ((array) $postIds as $postId) {
            $this->button($postId);
        }
    }

    public function content($metaKey = false, $passthrough = false)
    {
        if ($passthrough) {
            $content = $metaKey;
        }
        if (!isset($content)) {
            $content = $metaKey
                ? $this->postmeta->get($metaKey)
                : get_the_content();
        }
        echo str_replace(']]>', ']]&gt;', apply_filters('the_content', $content));
    }

    public function copyright(array $args = [])
    {
        $args = shortcode_atts([
            'copyright' => sprintf('<span>%s </span>&copy;', __('Copyright', 'castor')),
            'date' => date('Y'),
            'name' => get_bloginfo('name'),
            'separator' => '&mdash;',
        ], $args);
        extract($args);
        if ($separator) {
            $separator .= ' ';
        }
        printf('%s %s %s%s', $copyright, $date, $separator, $name);
    }

    public function featured($args = [])
    {
        $args = wp_parse_args($args, [
            'class' => 'featured',
            'image' => get_post_thumbnail_id(),
            'player' => '',
            'video' => 'featured_video',
        ]);
        $featuredHtml = $this->media->video(wp_parse_args($args, [
            'url' => $args['video'],
        ]));
        if (empty($featuredHtml) && $featuredImage = $this->media->getImage($args['image'])) {
            $featuredCaption = $featuredImage->caption
                ? sprintf('<figcaption>%s</figcaption>', $featuredImage->caption)
                : '';
            $featuredHtml = sprintf('<div class="featured-image"><img src="%s" alt="%s"></div>%s',
                $featuredImage->large['url'],
                $featuredImage->alt,
                $featuredCaption
            );
        }
        if (!empty($featuredHtml)) {
            printf('<figure class="%s">%s</figure>', $args['class'], $featuredHtml);
        }
    }

    public function field($name, array $args = [])
    {
    }

    public function form($name, array $args = [])
    {
    }

    public function gallery(array $args = [])
    {
        echo $this->media->gallery($args);
    }

    public function h1($string, array $attributes = [])
    {
        $this->utility->printTag('h1', wp_strip_all_tags($string), $attributes);
    }

    public function h2($string, array $attributes = [])
    {
        $this->utility->printTag('h2', wp_strip_all_tags($string), $attributes);
    }

    public function h3($string, array $attributes = [])
    {
        $this->utility->printTag('h3', wp_strip_all_tags($string), $attributes);
    }

    public function h4($string, array $attributes = [])
    {
        $this->utility->printTag('h4', wp_strip_all_tags($string), $attributes);
    }

    public function h5($string, array $attributes = [])
    {
        $this->utility->printTag('h5', wp_strip_all_tags($string), $attributes);
    }

    public function h6($string, array $attributes = [])
    {
        $this->utility->printTag('h6', wp_strip_all_tags($string), $attributes);
    }

    public function madeWithLove($name)
    {
        printf(__('Made with %s by %s', 'castor'),
            file_get_contents(sprintf('%simg/heart.svg', \GeminiLabs\Castor\Application::getInstance()->assets)),
            $name
        );
    }

    public function p($string, array $attributes = [])
    {
        $this->utility->printTag('p', wp_strip_all_tags($string), $attributes);
    }

    public function title($metaKey = false, array $attributes = [])
    {
        $tag = apply_filters('castor/render/title/tag', 'h2');
        $value = $metaKey
            ? $this->postmeta->get($metaKey)
            : $this->theme->pageTitle();

        if (!$value) {
            return;
        }

        $this->utility->printTag($tag, wp_strip_all_tags($value), $attributes);
    }

    /**
     * @param array|string $args
     *
     * @return string|null
     */
    public function video($args)
    {
        echo $this->media->video($args);
    }
}
