<?php

/**
 * @package    Elephant City
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Type;

use Exception;

/**
 * Class WorkflowTypeNotFound
 *
 * @package Netzmacht\Contao\Workflow\Type
 */
class WorkflowTypeNotFound extends Exception
{
    /**
     * Create exception for a workflow type name.
     *
     * @param string $name Workflow type name.
     *
     * @return static
     */
    public static function withName($name)
    {
        return new static(sprintf('Workflow type "%s" not found', $name));
    }
}
