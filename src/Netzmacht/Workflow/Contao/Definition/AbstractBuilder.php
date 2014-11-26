<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Definition;

/**
 * Class AbstractBuilder is the base class for all workflow builders.
 *
 * @package Netzmacht\Workflow\Contao\Definition\Builder
 */
class AbstractBuilder
{
    /**
     * Get a service form the ervice container.
     *
     * @param string $name Name of the service.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getService($name)
    {
        return $GLOBALS['container'][$name];
    }
}
