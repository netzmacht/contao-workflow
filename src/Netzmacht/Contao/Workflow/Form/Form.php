<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Form;

/**
 * Interface Form describes a form instance which is used for workflow transition.
 *
 * @package Netzmacht\Contao\Workflow\Form
 */
interface Form
{
    /**
     * Validate form data.
     *
     * @return bool
     */
    public function validate();

    /**
     * Render the form and return it as string.
     *
     * @return string
     */
    public function render();

    /**
     * Add label and description for a fieldset.
     *
     * @param string $name        Name of the fieldset.
     * @param string $label       Label of the fieldset.
     * @param string $description Description in the fieldset.
     * @param string $class       Optional css class.
     *
     * @return $this
     */
    public function addFieldset($name, $label, $description = null, $class = null);

    /**
     * Add a field to the form.
     *
     * @param string $name     Name of the field
     * @param string $type     Widget type name.
     * @param mixed  $default  Optional default value.
     * @param array  $config   Widget configuration.
     * @param array  $options  Options.
     * @param string $fieldset Legend to which the widget should be grouped.
     *
     * @return $this
     */
    public function addField(
        $name,
        $type,
        $default=null,
        array $config = array(),
        array $options = null,
        $fieldset = 'default'
    );

    /**
     * Get form data of an validated form.
     *
     * @return array
     */
    public function getData();
}
