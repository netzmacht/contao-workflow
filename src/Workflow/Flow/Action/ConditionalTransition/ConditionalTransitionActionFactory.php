<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2020 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ConditionalTransition;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class ConditionalTransitionActionFactory
 *
 * @package Netzmacht\ContaoWorkflowBundle\Flow\Action\ConditionalTransition
 */
final class ConditionalTransitionActionFactory implements ActionTypeFactory
{
    /**
     * {@inheritDoc}
     */
    public function getCategory(): string
    {
        return 'default';
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'conditionaltransition';
    }

    /**
     * {@inheritDoc}
     */
    public function isPostAction(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Workflow $workflow): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config, Transition $transition): Action
    {
        return new ConditionalTransitionAction(
            'action_' . $config['id'],
            $config['label'],
            $config['conditionaltransitions'],
            $config
        );
    }
}
