<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Contao\Dca;


class Common
{
    public function generateRow(array $row)
    {
        return sprintf(
            '<strong>%s</strong> <span class="tl_gray">[%s]</span><br>%s',
            $row['label'],
            $row['name'],
            $row['description']
        );
    }

    /**
     * @param $value
     * @param $dataContainer
     *
     * @return string
     */
    public function createName($value, $dataContainer)
    {
        if (!$value) {
            $value = $dataContainer->activeRecord->label;
        }

        return standardize($value);
    }
}
