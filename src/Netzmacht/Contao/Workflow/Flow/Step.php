<?php

namespace Netzmacht\Contao\Workflow\Flow;

use Netzmacht\Contao\Workflow\Base;

/**
 * Class Step defines fixed step in the workflow process.
 *
 * @package Netzmacht\Contao\Workflow\Flow
 */
class Step extends Base
{
    /**
     * The allowed transition names.
     *
     * @var array
     */
    private $allowedTransitions = array();

    /**
     * Step is a final step.
     *
     * @var bool
     */
    private $final = false;

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * Consider if step is final.
     *
     * @return boolean
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * Mark step as final.
     *
     * @param boolean $final Step is a final step.
     *
     * @return $this
     */
    public function setFinal($final)
    {
        $this->final = (bool)$final;

        return $this;
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

    /**
     * Allow a transition.
     *
     * @param string $transitionName The name of the allowed transition.
     *
     * @return $this
     */
    public function allowTransition($transitionName)
    {
        if (!in_array($transitionName, $this->allowedTransitions)) {
            $this->allowedTransitions[] = $transitionName;
        }

        return $this;
    }

    /**
     * Disallow a transition.
     *
     * @param string $transitionName The name of the disallowed transition.
     *
     * @return $this
     */
    public function disallowTransition($transitionName)
    {
        $key = array_search($transitionName, $this->allowedTransitions);

        if ($key !== false) {
            unset($this->allowedTransitions[$key]);
            $this->allowedTransitions = array_values($this->allowedTransitions);
        }

        return $this;
    }

    /**
     * Get all allowed transition names.
     *
     * @return array
     */
    public function getAllowedTransitions()
    {
        if ($this->isFinal()) {
            return array();
        }

        return $this->allowedTransitions;
    }

    /**
     * Consider if transition is allowed.
     *
     * @param string $transitionName The name of the checked transition.
     *
     * @return bool
     */
    public function isTransitionAllowed($transitionName)
    {
        if ($this->isFinal()) {
            return false;
        }

        return in_array($transitionName, $this->allowedTransitions);
    }
}
