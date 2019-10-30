<?php

namespace GeminiLabs\Castor\Helpers;

class Utility
{
    /**
     * @return string
     */
    public function buildAttributes(array $atts = [])
    {
        $attributes = [];
        foreach ($atts as $key => $value) {
            $attributes[] = sprintf('%s="%s"', $key, $value);
        }
        return implode(' ', $attributes);
    }

    /**
     * @return string
     */
    public function buildAttributesFor($tag, array $atts = [])
    {
        return $this->buildAttributes(
            wp_parse_args($atts, apply_filters("castor/render/$tag/attributes", []))
        );
    }

    /**
     * @param string $name
     * @param string $path
     *
     * @return string
     */
    public function buildClassName($name, $path = '')
    {
        $className = array_map('ucfirst', array_map('strtolower', (array) preg_split('/[-_]/', $name)));
        $className = implode('', $className);

        return !empty($path)
            ? str_replace('\\\\', '\\', sprintf('%s\%s', $path, $className))
            : $className;
    }

    /**
     * @param string $name
     * @param string $prefix
     *
     * @return string
     */
    public function buildMethodName($name, $prefix = 'get')
    {
        return lcfirst($this->buildClassName($prefix.'-'.$name));
    }

    /**
     * @param string $needle
     * @param string $haystack
     *
     * @return bool
     */
    public function contains($needle, $haystack)
    {
        false !== strpos($haystack, $needle);
    }

    /**
     * @param string $suffix
     * @param string $string
     * @param bool   $unique
     *
     * @return string
     */
    public function endWith($suffix, $string, $unique = true)
    {
        return $unique && $this->endsWith($suffix, $string)
            ? $string
            : $string.$suffix;
    }

    /**
     * @param string $needle
     * @param string $haystack
     *
     * @return bool
     */
    public function endsWith($needle, $haystack)
    {
        $length = strlen($needle);
        return 0 != $length
            ? substr($haystack, -$length) === $needle
            : true;
    }

    /**
     * @param string $tag
     * @param string $value
     *
     * @return void
     */
    public function printTag($tag, $value, array $attributes = [])
    {
        $attributes = $this->buildAttributesFor($tag, $attributes);

        printf('<%s>%s</%s>',
            rtrim(sprintf('%s %s', $tag, $attributes)),
            $value,
            $tag
        );
    }

    /**
     * @param string $prefix
     * @param string $string
     * @param bool   $unique
     *
     * @return string
     */
    public function startWith($prefix, $string, $unique = true)
    {
        return $unique && $this->startsWith($prefix, $string)
            ? $string
            : $prefix.$string;
    }

    /**
     * @param string $needle
     * @param string $haystack
     *
     * @return bool
     */
    public function startsWith($needle, $haystack)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    /**
     * @param mixed $value
     *
     * @return array
     */
    public function toArray($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        return array_filter((array) $value);
    }

    /**
     * @param string $string
     * @param string $needle
     * @param bool   $caseSensitive
     *
     * @return string
     */
    public function trimLeft($string, $needle, $caseSensitive = true)
    {
        $strPos = $caseSensitive ? 'strpos' : 'stripos';
        if (0 === $strPos($string, $needle)) {
            $string = substr($string, strlen($needle));
        }
        return $string;
    }

    /**
     * @param string $string
     * @param string $needle
     * @param bool   $caseSensitive
     *
     * @return string
     */
    public function trimRight($string, $needle, $caseSensitive = true)
    {
        $strPos = $caseSensitive ? 'strpos' : 'stripos';
        if (false !== $strPos($string, $needle, strlen($string) - strlen($needle))) {
            $string = substr($string, 0, -strlen($needle));
        }
        return $string;
    }
}
