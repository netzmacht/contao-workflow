<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Backend\Dca;


class Workflow
{
    /**
     * Get names of workflow types
     *
     * @return array
     */
    public function getTypes()
    {
        return array_keys($GLOBALS['WORKFLOW_TYPES']);
    }

} 