<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Form\Contao;

use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\Properties\DefaultProperty;
use Netzmacht\Workflow\Contao\Form\FormField;

/**
 * Class BackendFormField is used for a backend form.
 *
 * @package Netzmacht\Workflow\Contao\Form
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
