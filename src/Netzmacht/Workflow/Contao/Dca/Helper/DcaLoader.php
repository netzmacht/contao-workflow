<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Dca\Helper;


class DcaLoader extends \Controller
{
    public static function load($dataContainerName, $noCache=false)
    {
        $loader = new DcaLoader();
        $loader->loadDataContainer($dataContainerName, $noCache);
    }
}
