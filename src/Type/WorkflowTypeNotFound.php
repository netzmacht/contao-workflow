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

namespace Netzmacht\ContaoWorkflowBundle\Type;

use Exception;

/**
 * Class WorkflowTypeNotFound
 *
 * @package Netzmacht\ContaoWorkflowBundle\Type
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
