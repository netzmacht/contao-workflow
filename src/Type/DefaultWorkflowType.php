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
 * Class DefaultWorkflowType.
 *
 * @package Netzmacht\Contao\Workflow\Type
 */
class DefaultWorkflowType implements WorkflowType
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'default';
    }

    /**
     * @inheritDoc
     */
    public function hasFixedSteps()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getStepNames()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function hasFixedTransitions()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getTransitionNames()
    {
        return [];
    }
}
