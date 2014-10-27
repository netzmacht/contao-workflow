<?php

namespace Netzmacht\Contao\Workflow\Flow;


use Assert\Assertion;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Exception\StepNotFoundException;
use Netzmacht\Contao\Workflow\Flow\Exception\TransitionNotAllowedException;
use Netzmacht\Contao\Workflow\Flow\Exception\TransitionNotFoundException;
use Netzmacht\Contao\Workflow\Flow\Exception\ProcessNotStartedException;
use Netzmacht\Contao\Workflow\Flow\Workflow\Condition;

class Workflow
{
    /**
     * @var Transition[]
     */
    private $transitions = array();

    /**
     * @var Step[]
     */
    private $steps = array();

    /**
     * @var Transition
     */
    private $startTransition;

    /**
     * @var Condition
     */
    private $condition;

    /**
     * @var string
     */
    private $name;

    /**
     * @param array $steps
     * @param array $transitions
     * @param $startTransitionName
     * @param \Netzmacht\Contao\Workflow\Flow\Workflow\Condition $condition
     *
     * @throws TransitionNotFoundException
     */
    public function __construct(array $steps, array $transitions, $startTransitionName, Condition $condition = null)
    {
        Assertion::allIsInstanceOf($steps, 'Netzmacht\Contao\Workflow\Flow\Step');
        Assertion::allIsInstanceOf($transitions, 'Netzmacht\Contao\Workflow\Flow\Transition');

        $this->transitions     = $transitions;
        $this->steps           = $steps;
        $this->startTransition = $this->getTransition($startTransitionName);
        $this->condition       = $condition;
    }


    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $transitionName
     * @throws TransitionNotFoundException
     * @return \Netzmacht\Contao\Workflow\Flow\Transition
     */
    public function getTransition($transitionName)
    {
        foreach ($this->transitions as $transition) {
            if ($transition->getName() == $transitionName) {
                return $transition;
            }
        }

        throw new TransitionNotFoundException($transitionName);
    }

    /**
     * @param $stepName
     * @return Step
     * @throws StepNotFoundException
     */
    public function getStep($stepName)
    {
        foreach ($this->steps as $step) {
            if ($step->getName() == $stepName) {
                return $step;
            }
        }

        throw new StepNotFoundException($stepName);
    }

    /**
     * @return Transition
     */
    public function getStartTransition()
    {
        return $this->startTransition;
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function match(Entity $entity)
    {
        if ($this->condition) {
            return $this->condition->match($entity);
        }

        return false;
    }

    /**
     * @param Entity          $entity
     * @param                 $transitionName
     * @param Context         $context
     *
     * @throws ProcessNotStartedException
     * @throws StepNotFoundException
     * @throws TransitionNotAllowedException
     * @throws TransitionNotFoundException
     *
     * @return \Netzmacht\Contao\Workflow\Flow\State
     */
    public function transit(Entity $entity, $transitionName, Context $context)
    {
        $this->guardWorkflowStarted($entity);

        $state       = $entity->getState();
        $currentStep = $this->getStep($state->getStepName());

        $this->guardTransitionAllowed($currentStep, $transitionName);

        $transition = $this->getTransition($transitionName);

        return $transition->transit($entity, $context);
    }

    /**
     * @param Entity          $entity
     * @param Context         $context
     *
     * @return \Netzmacht\Contao\Workflow\Flow\State
     */
    public function start(Entity $entity, Context $context)
    {
        if ($entity->getState()) {
            return $entity->getState();
        }

        $state = State::init();
        $entity->transit($state);

        $transition = $this->getStartTransition();

        return $transition->transit($entity, $context);
    }

    /**
     * @param Entity $entity
     *
     * @throws ProcessNotStartedException
     */
    private function guardWorkflowStarted(Entity $entity)
    {
        $state = $entity->getState();

        if (!$state) {
            throw new ProcessNotStartedException();
        }
    }

    /**
     * @param $currentStep
     * @param $transitionName
     * @throws TransitionNotAllowedException
     */
    private function guardTransitionAllowed(Step $currentStep, $transitionName)
    {
        if (!$currentStep->isTransitionAllowed($transitionName)) {
            throw new TransitionNotAllowedException($currentStep->getName(), $transitionName);
        }
    }
}
