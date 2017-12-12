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

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event;

use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateStepEvent is emitted when a workflow step is created.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Definition\Event
 */
class CreateStepEvent extends Event
{
    const NAME = 'netzmacht.contao_workflow.create_step';

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
     *
     * @return Step
     */
    public function getStep(): Step
    {
        return $this->step;
    }

    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }
}
