<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated;

use Netzmacht\Contao\WorkflowDeprecated\View\Form;

class View
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @return Form
     */
    public function createForm($id)
    {
        return new Form('workflow_' . $id, 'POST', array($this, 'isSubmit'));
    }

    public function forceDisplay($force=true)
    {
    }
}