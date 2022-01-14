<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event;

use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class CreateStepEvent is emitted when a workflow step is created.
 */
final class CreateStepEvent extends Event
{
    public const NAME = 'netzmacht.contao_workflow.create_step';

    /**
     * The workflow this step belongs to.
     *
     * @var Workflow
     */
    private $workflow;


    /**
     * The workflow step.
     *
     * @var Step
     */
    private $step;

    /**
     * Construct.
     *
     * @param Workflow $workflow The workflow.
     * @param Step     $step     The step which is build.
     */
    public function __construct(Workflow $workflow, Step $step)
    {
        $this->workflow = $workflow;
        $this->step     = $step;
    }

    /**
     * Get the step.
     */
    public function getStep(): Step
    {
        return $this->step;
    }

    /**
     * Get the workflow.
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }
}
