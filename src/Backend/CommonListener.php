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

declare(strict_types=1);

namespace Netzmacht\Contao\Workflow\Backend;

use Contao\DataContainer;
use Contao\StringUtil;

/**
 * Class Common provides common helper functionalities.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class CommonListener
{
    /**
     * Generate a row view.
     *
     * @param array $row Current data row.
     *
     * @return string
     */
    public function generateRow(array $row): string
    {
        return sprintf(
            '<strong>%s</strong> <span class="tl_gray">[%s]</span><br>%s',
            $row['label'],
            $row['name'],
            $row['description']
        );
    }

    /**
     * Create the name.
     *
     * @param string        $value         Current name value.
     * @param DataContainer $dataContainer The Dc_Table.
     *
     * @return string
     */
    public function createName($value, $dataContainer): string
    {
        if (!$value) {
            $value = $dataContainer->activeRecord->label;
        }

        return StringUtil::standardize($value);
    }
}
