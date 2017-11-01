<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Form\Contao;

use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\Properties\DefaultProperty;
use Netzmacht\Contao\Workflow\Form\FormField;

/**
 * Class BackendFormField is used for a backend form.
 *
 * @package Netzmacht\Contao\Workflow\Form
 */
class ContaoFormField extends DefaultProperty implements FormField
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
}
