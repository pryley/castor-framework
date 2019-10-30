<?php

namespace GeminiLabs\Castor\Forms;

use Exception;
use GeminiLabs\Castor\Services\Normalizer;

class Form
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var array
     */
    protected $dependencies;

    /**
     * @var Field
     */
    protected $field;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var Normalizer
     */
    protected $normalize;

    public function __construct(Field $field, Normalizer $normalize)
    {
        $this->args = [];
        $this->dependencies = [];
        $this->field = $field;
        $this->fields = [];
        $this->normalize = $normalize;
    }

    /**
     * @param string $property
     *
     * @return mixed
     * @throws Exception
     */
    public function __get($property)
    {
        switch ($property) {
            case 'args':
            case 'dependencies':
            case 'fields':
                return $this->$property;
        }
        throw new Exception(sprintf('Invalid %s property: %s', __CLASS__, $property));
    }

    /**
     * @param string $property
     * @param string $value
     *
     * @return void
     * @throws Exception
     */
    public function __set($property, $value)
    {
        switch ($property) {
            case 'args':
            case 'dependencies':
            case 'fields':
                $this->$property = $value;
                break;
            default:
                throw new Exception(sprintf('Invalid %s property: %s', __CLASS__, $property));
        }
    }

    /**
     * Add a field to the form.
     *
     * @return Form
     */
    public function addField(array $args = [])
    {
        $field = $this->field->normalize($args);

        if (false !== $field->args['render']) {
            $this->dependencies = array_unique(
                array_merge($field->dependencies, $this->dependencies)
            );
            $this->fields[] = $field;
        }

        return $this;
    }

    /**
     * Normalize the form arguments.
     *
     * @return Form
     */
    public function normalize(array $args = [])
    {
        $defaults = [
            'action' => '',
            'attributes' => '',
            'id' => '',
            'class' => '',
            'enctype' => 'multipart/form-data',
            'method' => 'post',
            'nonce' => '',
            'submit' => __('Submit', 'site-reviews'),
        ];

        $this->args = array_merge($defaults, $args);

        $attributes = $this->normalize->form($this->args, 'implode');

        $this->args['attributes'] = $attributes;

        return $this;
    }

    /**
     * Render the form.
     *
     * @param mixed $print
     *
     * @return string|void
     */
    public function render($print = true)
    {
        $rendered = sprintf('<form %s>%s%s</form>',
            $this->args['attributes'],
            $this->generateFields(),
            $this->generateSubmitButton()
        );

        if ((bool) $print && 'return' !== $print) {
            echo $rendered;
        }

        return $rendered;
    }

    /**
     * Reset the Form.
     *
     * @return Form
     */
    public function reset()
    {
        $this->args = [];
        $this->dependencies = [];
        $this->fields = [];
        return $this;
    }

    /**
     * Generate the form fields.
     *
     * @return string
     */
    protected function generateFields()
    {
        $hiddenFields = '';

        $fields = array_reduce($this->fields, function ($carry, $formField) use (&$hiddenFields) {
            $stringLegend = '<legend class="screen-reader-text"><span>%s</span></legend>';
            $stringFieldset = '<fieldset%s>%s%s</fieldset>';
            $stringRendered = '<tr class="glsr-field %s"><th scope="row">%s</th><td>%s</td></tr>';
            $outsideRendered = '</tbody></table>%s<table class="form-table"><tbody>';

            // set field value only when rendering because we need to check the default setting
            // against the database
            $field = $formField->setValue()->getField();

            $multi = true === $field->multi;
            $label = $field->generateLabel();
            $rendered = $field->render();

            // render hidden inputs outside the table
            if ('hidden' === $field->args['type']) {
                $hiddenFields .= $rendered;
                return $carry;
            }

            $hiddenClass = $this->isFieldHidden($formField) ? 'hidden' : '';

            if ($multi) {
                if ($depends = $formField->getDataDepends()) {
                    $depends = sprintf(' data-depends=\'%s\'', json_encode($depends));
                }

                $legend = $label ? sprintf($stringLegend, $label) : '';
                $rendered = sprintf($stringFieldset, $depends, $legend, $rendered);
            }

            $renderedField = $field->outside
                ? sprintf($outsideRendered, $rendered)
                : sprintf($stringRendered, $hiddenClass, $label, $rendered);

            return $carry.$renderedField;
        });

        return sprintf('<table class="form-table"><tbody>%s</tbody></table>%s', $fields, $hiddenFields);
    }

    /**
     * Generate the form submit button.
     *
     * @return string|null
     */
    protected function generateSubmitButton()
    {
        $args = $this->args['submit'];

        is_array($args) ?: $args = ['text' => $args];

        $args = shortcode_atts([
            'text' => __('Save Changes', 'site-reviews'),
            'type' => 'primary',
            'name' => 'submit',
            'wrap' => true,
            'other_attributes' => null,
        ], $args);

        if (is_admin()) {
            ob_start();
            submit_button($args['text'], $args['type'], $args['name'], $args['wrap'], $args['other_attributes']);
            return ob_get_clean();
        }
    }

    /**
     * @param object $field GeminiLabs\Castor\Form\Fields\*
     *
     * @return bool|null
     */
    protected function isFieldHidden($field)
    {
        if (!($dependsOn = $field->getDataDepends())) {
            return;
        }

        foreach ($this->fields as $formField) {
            if ($dependsOn['name'] !== $formField->args['name']) {
                continue;
            }

            if (is_array($dependsOn['value'])) {
                return !in_array($formField->args['value'], $dependsOn['value']);
            }

            return $dependsOn['value'] != $formField->args['value'];
        }
    }
}
