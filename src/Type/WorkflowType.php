<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Type;

/**
 * Interface WorkflowType.
 *
 * @package Netzmacht\Contao\Workflow\Type
 */
interface WorkflowType
{
    /**
     * Get the name of the workflow.
     *
     * @return string
     */
    public function getName();

    /**
     * Get information if workflow type only supports a set of fixed steps.
     *
     * @return bool
     */
    public function hasFixedSteps();

    /**
     * Get the names of the fixed steps.
     *
     * @return array
     */
    public function getStepNames();

    /**
     * Get information if workflow type only supports a set of fixed transitions.
     *
     * @return bool
     */
    public function hasFixedTransitions();

    /**
     * Get the names of the fixed transitions.
     *
     * @return array
     */
    public function getTransitionNames();
}
