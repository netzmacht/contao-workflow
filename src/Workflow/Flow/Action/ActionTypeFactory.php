<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Interface ActionFactory
 *
 * @package Netzmacht\ContaoWorkflowBundle\Action
 */
interface ActionTypeFactory
{
    /**
     * Get the category.
     *
     * @return string
     */
    public function getCategory(): string;

    /**
     * Get name of the action.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns true if action as to be registered as post action.
     *
     * @return bool
     */
    public function isPostAction(): bool;

    /**
     * Check if workflow is supported.
     *
     * @param Workflow $workflow The workflow in which the action should be handled.
     *
     * @return bool
     */
    public function supports(Workflow $workflow): bool;

    /**
     * Create an action.
     *
     * @param array      $config     Action config.
     * @param Transition $transition Transition to which the action belongs.
     *
     * @return Action
     */
    public function create(array $config, Transition $transition): Action;
}
