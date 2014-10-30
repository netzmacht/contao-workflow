<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Contao;


class DataContainer extends \Controller
{
    public static function load($dataContainerName, $noCache=false)
    {
        $loader = new DataContainer();
        $loader->loadDataContainer($dataContainerName, $noCache);
    }

}
