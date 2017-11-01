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

namespace Netzmacht\Contao\Workflow\Definition\Event;

use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateWorkflowEvent is dispatched when creating a worfklow.
 *
 * @package Netzmacht\Contao\Workflow\Factory\Event
 */
class CreateWorkflowEvent extends Event
{
    const NAME = 'workflow.factory.create-workflow';

    /**
     * The workflow being created.
     *
     * @var Workflow
     */
    private $workflow;

    /**
     * Construct.
     *
     * @param Workflow $workflow Workflow being created.
     */
    public function __construct(Workflow $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }
}
