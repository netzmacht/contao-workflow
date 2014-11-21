<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Definition\Event;

use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateStepEvent is emitted when a workflow step is created.
 *
 * @package Netzmacht\Workflow\Contao\Definition\Event
 */
class CreateStepEvent extends Event
{
    const NAME = 'workflow.factory.create-step';

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
    public function getStep()
    {
        return $this->step;
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
