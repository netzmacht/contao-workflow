<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Factory;

use Assert\Assertion;
use Netzmacht\Contao\Workflow\Flow\Step;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Flow\Workflow\Condition;

class WorkflowFactory
{
    /**
     * Set of steps.
     *
     * @var Step[]|array
     */
    private $steps;

    /**
     * Set of transitions.
     *
     * @var Transition[]|array
     */
    private $transitions;

    /**
     * Name of the start transition.
     *
     * @var string
     */
    private $startTransition;

    /**
     * Extra configuration.
     *
     * @var array
     */
    private $config = array();

    /**
     * Name of the workflow.
     *
     * @var string
     */
    private $name;

    /**
     * Name of the label.
     *
     * @var string
     */
    private $label;

    /**
     * Condition
     *
     * @var condition
     */
    private $condition;

    /**
     * @return Condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Add a condition.
     *
     * @param Condition $condition Workflow condition.
     *
     * @return $this
     */
    public function addCondition(Condition $condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Get the config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the config.
     *
     * @param array $config Configuration array.
     *
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get the label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label.
     *
     * @param string $label Workflow label.
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string $name Workflow name.
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get start transition name.
     *
     * @return string
     */
    public function getStartTransitionName()
    {
        return $this->startTransition;
    }

    /**
     * Get all steps.
     *
     * @return array|Step[]
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * Add a step to workflow.
     *
     * @param Step $step Workflow step.
     *
     * @return $this
     */
    public function addStep(Step $step)
    {
        $this->steps[] = $step;

        return $this;
    }

    /**
     * Get workflow transitions.
     *
     * @return array|Transition[]
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * Add new transition to the workflow.
     *
     * @param Transition $transition
     * @param bool       $startTransition
     *
     * @return $this
     */
    public function addTransition(Transition $transition, $startTransition = false)
    {
        $this->transitions[] = $transition;

        if ($startTransition) {
            $this->startTransition = $transition->getName();
        }

        return $this;
    }

    /**
     * Create the workflow.
     *
     * @return Workflow
     */
    public function create()
    {
        Assertion::notNull($this->name, 'Workflow name has to be set');
        Assertion::notNull($this->startTransition, 'Start transition has to be set');

        return new Workflow(
            $this->name,
            $this->steps,
            $this->transitions,
            $this->startTransition,
            $this->condition,
            $this->label,
            $this->config
        );
    }
}
