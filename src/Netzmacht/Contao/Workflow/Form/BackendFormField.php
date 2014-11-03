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

use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\Properties\DefaultProperty;

class BackendFormField extends DefaultProperty implements FormField
{
    /**
     * @param string $name
     * @param        $type
     */
    public function __construct($name, $type)
    {
        parent::__construct($name);

        $this->setWidgetType($type);
    }

    /**
     * Add field to a form.
     *
     * @param Form $form
     *
     * @return $this
     */
    public function addToForm(Form $form, $fieldset = 'default')
    {
        $form->addField($this, $fieldset);

        return $this;
    }
}
