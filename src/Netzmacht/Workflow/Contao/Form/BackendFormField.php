<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Form;

use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\Properties\DefaultProperty;

/**
 * Class BackendFormField is used for a backend form.
 *
 * @package Netzmacht\Workflow\Contao\Form
 */
class BackendFormField extends DefaultProperty implements FormField
{
    /**
     * Construct.
     *
     * @param string $name Name of form field.
     * @param string $type Type of the form field.
     */
    public function __construct($name, $type)
    {
        parent::__construct($name);

        $this->setWidgetType($type);
    }

    /**
     * Add field to a form.
     *
     * @param Form   $form     Form which will get the widget.
     * @param string $fieldset The name of the fieldset being used.
     *
     * @return $this
     */
    public function addToForm(Form $form, $fieldset = 'default')
    {
        $form->addField($this, $fieldset);

        return $this;
    }
}
