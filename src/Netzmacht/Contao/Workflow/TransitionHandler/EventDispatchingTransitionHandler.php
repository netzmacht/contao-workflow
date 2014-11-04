<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\TransitionHandler;

use Netzmacht\Workflow\Handler\Event\BuildFormEvent;
use Netzmacht\Workflow\Handler\Event\ValidateTransitionEvent;
use Netzmacht\Workflow\Handler\Event\PostTransitionEvent;
use Netzmacht\Contao\Workflow\Flow;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Exception\Flow\InvalidTransitionException;
use Netzmacht\Contao\Workflow\Exception\Flow\WorkflowException;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class EventDispatchingTransitionHandler raises events during the transition being processed.
 *
 * @package Netzmacht\Contao\Workflow\TransitionHandler
 */
class EventDispatchingTransitionHandler implements TransitionHandler
{
    /**
     * The transition handler being internally used.
     *
     * @var \Netzmacht\Workflow\Flow\\Netzmacht\Workflow\\Netzmacht\Workflow\Handler\TransitionHandler
     */
    private $transitionHandler;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Store if form was built.
     *
     * @var bool
     */
    private $formBuilt;

    /**
     * Store if input is required.
     *
     * @var bool
     */
    private $isInputRequired;

    /**
     * Store if transition got validated.
     *
     * @var bool
     */
    private $validated;

    /**
     * Construct.
     *
     * @param \Netzmacht\Workflow\Flow\\Netzmacht\Workflow\\Netzmacht\Workflow\Handler\TransitionHandler $transitionHandler The internally used transition handler.
     * @param EventDispatcher   $eventDispatcher   The event dispatcher for dispatching the evens.
     */
    public function __construct(TransitionHandler $transitionHandler, EventDispatcher $eventDispatcher)
    {
        $this->transitionHandler = $transitionHandler;
        $this->eventDispatcher   = $eventDispatcher;
    }

    /**
     * Get the workflow.
     *
     * @return \Netzmacht\Workflow\Flow\Workflow
     */
    public function getWorkflow()
    {
        return $this->transitionHandler->getWorkflow();
    }

    /**
     * Get the item.
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->transitionHandler->getItem();
    }

    /**
     * Get the input form.
     *
     * @return Form
     */
    public function getForm()
    {
        $form = $this->transitionHandler->getForm();

        // dispatch event when first requesting the form
        if (!$this->formBuilt) {
            $event = new BuildFormEvent($this->getWorkflow(), $this->getTransition(), $this->getItem(), $form);
            $this->eventDispatcher->dispatch($event::NAME, $event);

            $this->isInputRequired = $event->isInputRequired();
            $this->formBuilt       = true;
        }

        return $form;
    }

    /**
     * Get the transition.
     *
     * @return \Netzmacht\Workflow\Flow\Transition
     *
     * @throws \Netzmacht\Contao\Workflow\Exception\Flow\TransitionNotFoundException If transition was not found.
     */
    public function getTransition()
    {
        return $this->transitionHandler->getTransition();
    }

    /**
     * Get current step. Will return null if workflow is not started yet.
     *
     * @return \Netzmacht\Workflow\Flow\Step|null
     */
    public function getCurrentStep()
    {
        return $this->transitionHandler->getCurrentStep();
    }

    /**
     * Consider if it handles a start transition.
     *
     * @return bool
     */
    public function isWorkflowStarted()
    {
        return $this->transitionHandler->isWorkflowStarted();
    }

    /**
     * Consider if input is required.
     *
     * @return bool
     */
    public function requiresInputData()
    {
        if ($this->isInputRequired) {
            return true;
        }

        return $this->transitionHandler->requiresInputData();
    }

    /**
     * Get the context.
     *
     * @return \Netzmacht\Workflow\Flow\Context
     */
    public function getContext()
    {
        return $this->transitionHandler->getContext();
    }

    /**
     * Validate the input.
     *
     * @param Form $form Transition form.
     *
     * @return bool
     */
    public function validate(Form $form)
    {
        if ($this->validated !== null) {
            return $this->validated;
        }

        if ($this->transitionHandler->validate($form)) {
            $event = new ValidateTransitionEvent(
                $this->getWorkflow(),
                $this->getTransition(),
                $this->getItem(),
                $this->getForm()
            );

            $this->eventDispatcher->dispatch($event::NAME, $event);

            $this->validated = $event->isValid();

            return $this->validated;
        }

        $this->validated = false;
        return false;
    }

    /**
     * Transit to next step.
     *
     * @throws InvalidTransitionException If transition does not exists or is not allowed to be called.
     * @throws \Exception                 If some actions throws an unknown exception.
     *
     * @return State
     */
    public function transit()
    {
        $state = $this->transitionHandler->transit();
        $this->dispatchStepReachedEvent($state);

        return $state;
    }

    /**
     * Dispatch a step reached event.
     *
     * @param State $state State being reached.
     *
     * @return void
     */
    private function dispatchStepReachedEvent(State $state)
    {
        $event = new PostTransitionEvent($this->getWorkflow(), $this->getItem(), $state);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }
}
