<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\View;

/**
 * Class ItemOverviewView is used to render the item overview.
 *
 * @package Netzmacht\Contao\Workflow\View
 */
class ItemOverviewView extends AbstractItemView
{
    /**
     * Render the view.
     *
     * @return string
     */
    public function render()
    {
        if (!$this->template) {
            $this->template = 'workflow_item_overview';
        }

        return $this->renderTemplate();
    }
}
