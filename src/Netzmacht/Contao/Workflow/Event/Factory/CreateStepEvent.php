<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Event\Factory;


use Netzmacht\Contao\Workflow\Flow\Step;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Symfony\Component\EventDispatcher\Event;

class CreateStepEvent extends Event
{
    const NAME = 'workflow.factory.create-step';

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var Step
     */
    private $step;

    /**
     * @param Workflow $workflow
     * @param Step     $step
     */
    public function __construct(Workflow $workflow, Step $step)
    {
        $this->workflow = $workflow;
        $this->step     = $step;
    }

    /**
     * @return Step
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }
}
