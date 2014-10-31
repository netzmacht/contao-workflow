<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow;

use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Exception\Flow\InvalidTransitionException;
use Netzmacht\Contao\Workflow\Exception\Flow\WorkflowException;
use Netzmacht\Contao\Workflow\Model\State;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Form\Form;

/**
 * Class TransitionHandler handles the transition to another step in the workflow.
 *
 * @package Netzmacht\Contao\Workflow
 */
interface TransitionHandler
{
    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow();

    /**
     * Get the item.
     *
     * @return Item
     */
    public function getItem();

    /**
     * Get the input form.
     *
     * @return Form
     */
    public function getForm();

    /**
     * Get the transition.
     *
     * @return Transition
     *
     * @throws Netzmacht\Contao\Workflow\Exception\Flow\TransitionNotFoundException If transition was not found.
     */
    public function getTransition();

    /**
     * Get current step. Will return null if workflow is not started yet.
     *
     * @return Flow\Step|null
     */
    public function getCurrentStep();

    /**
     * Consider if it handles a start transition.
     *
     * @return bool
     */
    public function isWorkflowStarted();

    /**
     * Consider if input is required.
     *
     * @return bool
     */
    public function requiresInputData();

    /**
     * Get the context.
     *
     * @return Context
     */
    public function getContext();

    /**
     * Validate the input.
     *
     * @return bool
     */
    public function validate();

    /**
     * @return State
     * @throws InvalidTransitionException
     * @throws WorkflowException
     * @throws \Exception
     */
    public function start();

    /**
     * Transit to next step.
     *
     * @throws InvalidTransitionException
     * @throws \Exception If some actions throws an unknown exception.
     * @return State
     */
    public function transit();

}
