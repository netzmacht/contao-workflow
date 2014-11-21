<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\View;

/**
 * Class ItemOverviewView is used to render the item overview.
 *
 * @package Netzmacht\Workflow\Contao\View
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
