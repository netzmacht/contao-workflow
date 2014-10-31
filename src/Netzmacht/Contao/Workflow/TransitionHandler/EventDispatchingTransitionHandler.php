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

use Netzmacht\Contao\Workflow\Event\TransitionHandler\BuildFormEvent;
use Netzmacht\Contao\Workflow\Event\TransitionHandler\ValidateTransitionEvent;
use Netzmacht\Contao\Workflow\Event\TransitionHandler\SepReachedEvent;
use Netzmacht\Contao\Workflow\Flow;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Exception\InvalidTransitionException;
use Netzmacht\Contao\Workflow\Flow\Exception\WorkflowException;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Form\Form;
use Netzmacht\Contao\Workflow\Item;
use Netzmacht\Contao\Workflow\Model\State;
use Netzmacht\Contao\Workflow\TransitionHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class EventDispatchingTransitionHandler raises events during the transition being processed.
 *
 * @package Netzmacht\Contao\Workflow\TransitionHandler
 */
class EventDispatchingTransitionHandler implements TransitionHandler
{
    /**
     * @var TransitionHandler
     */
    private $transitionHandler;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var bool
     */
    private $formBuilt;

    /**
     * @var bool
     */
    private $isInputRequired;

    /**
     * @var bool
     */
    private $validated;

    /**
     * @param TransitionHandler $transitionHandler
     * @param EventDispatcher   $eventDispatcher
     */
    function __construct(TransitionHandler $transitionHandler, EventDispatcher $eventDispatcher)
    {
        $this->transitionHandler = $transitionHandler;
        $this->eventDispatcher   = $eventDispatcher;
    }

    /**
     * Get the workflow.
     *
     * @return Workflow
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
            $this->formBuilt = true;
        }

        return $form;
    }

    /**
     * Get the transition.
     *
     * @return Transition
     *
     * @throws Flow\Exception\TransitionNotFoundException If transition was not found.
     */
    public function getTransition()
    {
        return $this->transitionHandler->getTransition();
    }

    /**
     * Get current step. Will return null if workflow is not started yet.
     *
     * @return Flow\Step|null
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
     * @return Context
     */
    public function getContext()
    {
        return $this->transitionHandler->getContext();
    }

    /**
     * Validate the input.
     *
     * @return bool
     */
    public function validate()
    {
        if ($this->validated !== null) {
            return $this->validated;
        }

        if ($this->transitionHandler->validate()) {
            $event = new ValidateTransitionEvent(
                $this->getWorkflow(),
                $this->getTransition(),
                $this->getItem(),
                $this->getForm()
            );

            $this->eventDispatcher->dispatch($event::NAME, $event);

            $this->validated = $event->isValid();
        }

        $this->validated = false;
        return false;
    }

    /**
     * @return State
     * @throws InvalidTransitionException
     * @throws WorkflowException
     * @throws \Exception
     */
    public function start()
    {
        $state = $this->transitionHandler->start();
        $this->dispatchStepReachedEvent($state);

        return $state;
    }

    /**
     * Transit to next step.
     *
     * @throws InvalidTransitionException
     * @throws \Exception If some actions throws an unknown exception.
     * @return State
     */
    public function transit()
    {
        $state = $this->transitionHandler->transit();
        $this->dispatchStepReachedEvent($state);

        return $state;
    }

    /**
     * @param $state
     */
    private function dispatchStepReachedEvent($state)
    {
        $event = new SepReachedEvent($this->getWorkflow(), $this->getItem(), $state);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }
}
