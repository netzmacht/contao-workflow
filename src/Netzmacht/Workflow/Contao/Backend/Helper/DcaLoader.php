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

namespace Netzmacht\Workflow\Contao\Backend\Helper;

/**
 * Class DcaLoader is just a helper class to overcome contao api limits.
 *
 * @package Netzmacht\Workflow\Contao\Dca\Helper
 */
class DcaLoader extends \Controller
{
    /**
     * Load a data container definition and return it as array.
     *
     * @param string $dataContainerName The data container name.
     * @param bool   $noCache           Ignore the cache.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function &load($dataContainerName, $noCache = false)
    {
        $loader = new DcaLoader();
        $loader->loadDataContainer($dataContainerName, $noCache);

        return $GLOBALS['TL_DCA'][$dataContainerName];
    }
}
