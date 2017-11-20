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

namespace Netzmacht\Contao\Workflow\Manager;

use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager\Manager as BaseManager;

/**
 * Interface Manager
 *
 * @package Netzmacht\Contao\Workflow\Manager
 */
interface Manager extends BaseManager
{
    /**
     * Get workflow by id.
     *
     * @param int $workflowId The workflow id.
     *
     * @return Workflow
     *
     * @throws WorkflowNotFound When no workflow is registered with the given id.
     */
    public function getWorkflowById(int $workflowId): Workflow;
}
