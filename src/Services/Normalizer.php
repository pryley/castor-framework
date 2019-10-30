<?php

namespace GeminiLabs\Castor\Services;

class Normalizer
{
    const BOOLEAN_ATTRIBUTES = [
        'autofocus', 'capture', 'checked', 'disabled', 'draggable', 'formnovalidate', 'hidden',
        'multiple', 'novalidate', 'readonly', 'required', 'selected', 'spellcheck',
        'webkitdirectory',
    ];

    const FORM_ATTRIBUTES = [
        'accept', 'accept-charset', 'action', 'autocapitalize', 'autocomplete', 'enctype',
        'method', 'name', 'novalidate', 'target',
    ];

    const GLOBAL_ATTRIBUTES = [
        'accesskey', 'class', 'contenteditable', 'contextmenu', 'dir', 'draggable', 'dropzone',
        'hidden', 'id', 'lang', 'spellcheck', 'style', 'tabindex', 'title',
    ];

    const GLOBAL_WILDCARD_ATTRIBUTES = [
        'aria-', 'data-', 'item', 'on',
    ];

    const INPUT_ATTRIBUTES = [
        'accept', 'autocapitalize', 'autocomplete', 'autocorrect', 'autofocus', 'capture',
        'checked', 'disabled', 'form', 'formaction', 'formenctype', 'formmethod',
        'formnovalidate', 'formtarget', 'height', 'incremental', 'inputmode', 'list', 'max',
        'maxlength', 'min', 'minlength', 'mozactionhint', 'multiple', 'name', 'pattern',
        'placeholder', 'readonly', 'required', 'results', 'selectionDirection', 'size', 'src',
        'step', 'type', 'value', 'webkitdirectory', 'width', 'x-moz-errormessage',
    ];

    const INPUT_TYPES = [
        'button', 'checkbox', 'color', 'date', 'datetime', 'datetime-local', 'email', 'file',
        'hidden', 'image', 'max', 'min', 'month', 'number', 'password', 'radio', 'range',
        'reset', 'search', 'step', 'submit', 'tel', 'text', 'time', 'url', 'value', 'week',
    ];

    const SELECT_ATTRIBUTES = [
        'autofocus', 'disabled', 'form', 'multiple', 'name', 'required', 'size',
    ];

    const TEXTAREA_ATTRIBUTES = [
        'autocapitalize', 'autocomplete', 'autofocus', 'cols', 'disabled', 'form', 'maxlength',
        'minlength', 'name', 'placeholder', 'readonly', 'required', 'rows',
        'selectionDirection', 'selectionEnd', 'selectionStart', 'wrap',
    ];

    /**
     * @var array
     */
    protected $args;

    public function __construct()
    {
        $this->args = [];
    }

    /**
     * Normalize form attributes.
     *
     * @return array|string
     */
    public function form(array $args = [], $implode = false)
    {
        $attributes = $this->parseAttributes(self::FORM_ATTRIBUTES, $args);

        return $this->maybeImplode($attributes, $implode);
    }

    /**
     * Normalize input attributes.
     *
     * @return array|string
     */
    public function input(array $args = [], $implode = false)
    {
        $this->filterInputType();

        $attributes = $this->parseAttributes(self::INPUT_ATTRIBUTES, $args);

        return $this->maybeImplode($attributes, $implode);
    }

    /**
     * Possibly implode attributes into a string.
     *
     * @param bool|string $implode
     *
     * @return array|string
     */
    public function maybeImplode(array $attributes, $implode = true)
    {
        if (!$implode || 'implode' !== $implode) {
            return $attributes;
        }
        $results = [];
        foreach ($attributes as $key => $value) {
            // if data attributes, use single quotes in case of json encoded values
            $quotes = false !== stripos($key, 'data-') ? "'" : '"';
            if (is_array($value)) {
                $value = json_encode($value);
                $quotes = "'";
            }
            $results[] = is_string($key)
                ? sprintf('%1$s=%3$s%2$s%3$s', $key, $value, $quotes)
                : $value;
        }
        return implode(' ', $results);
    }

    /**
     * Normalize select attributes.
     *
     * @return array|string
     */
    public function select(array $args = [], $implode = false)
    {
        $attributes = $this->parseAttributes(self::SELECT_ATTRIBUTES, $args);

        return $this->maybeImplode($attributes, $implode);
    }

    /**
     * Normalize textarea attributes.
     *
     * @return array|string
     */
    public function textarea(array $args = [], $implode = false)
    {
        $attributes = $this->parseAttributes(self::TEXTAREA_ATTRIBUTES, $args);

        return $this->maybeImplode($attributes, $implode);
    }

    /**
     * Filter attributes by the provided attrribute keys and remove any non-boolean keys
     * with empty values.
     *
     * @return array
     */
    protected function filterAttributes(array $attributeKeys)
    {
        $filtered = array_intersect_key($this->args, array_flip($attributeKeys));

        // normalize truthy boolean attributes
        foreach ($filtered as $key => $value) {
            if (!in_array($key, self::BOOLEAN_ATTRIBUTES)) {
                continue;
            }

            if (false !== $value) {
                $filtered[$key] = '';
                continue;
            }

            unset($filtered[$key]);
        }

        $filteredKeys = array_filter(array_keys($filtered), function ($key) use ($filtered) {
            return !(
                empty($filtered[$key])
                && !is_numeric($filtered[$key])
                && !in_array($key, self::BOOLEAN_ATTRIBUTES)
            );
        });

        return array_intersect_key($filtered, array_flip($filteredKeys));
    }

    /**
     * @return array
     */
    protected function filterGlobalAttributes()
    {
        $global = $this->filterAttributes(self::GLOBAL_ATTRIBUTES);

        $wildcards = [];

        foreach (self::GLOBAL_WILDCARD_ATTRIBUTES as $wildcard) {
            foreach ($this->args as $key => $value) {
                $length = strlen($wildcard);
                $result = substr($key, 0, $length) === $wildcard;

                if ($result) {
                    // only allow data attributes to have an empty value
                    if ('data-' != $wildcard && empty($value)) {
                        continue;
                    }

                    if (is_array($value)) {
                        if ('data-' != $wildcard) {
                            continue;
                        }

                        $value = json_encode($value);
                    }

                    $wildcards[$key] = $value;
                }
            }
        }

        return array_merge($global, $wildcards);
    }

    /**
     * @return void
     */
    protected function filterInputType()
    {
        if (!isset($this->args['type']) || !in_array($this->args['type'], self::INPUT_TYPES)) {
            $this->args['type'] = 'text';
        }
    }

    /**
     * @return array
     */
    protected function parseAttributes(array $attributes, array $args = [])
    {
        if (!empty($args)) {
            $this->args = array_change_key_case($args);
        }

        $global = $this->filterGlobalAttributes();
        $local = $this->filterAttributes($attributes);

        return array_merge($global, $local);
    }
}
