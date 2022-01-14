<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event;

use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class CreateWorkflowEvent is dispatched when creating a workflow.
 */
final class CreateWorkflowEvent extends Event
{
    public const NAME = 'netzmacht.contao_workflow.create_workflow';

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
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }
}
