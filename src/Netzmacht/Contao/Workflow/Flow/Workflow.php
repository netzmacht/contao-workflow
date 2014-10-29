<?php

namespace Netzmacht\Contao\Workflow\Flow;

use Assert\Assertion;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Contao\Workflow\Flow\Exception\StepNotFoundException;
use Netzmacht\Contao\Workflow\Flow\Exception\TransitionNotAllowedException;
use Netzmacht\Contao\Workflow\Flow\Exception\TransitionNotFoundException;
use Netzmacht\Contao\Workflow\Flow\Exception\ProcessNotStartedException;
use Netzmacht\Contao\Workflow\Flow\Workflow\Condition;
use Netzmacht\Contao\Workflow\Item;
use Netzmacht\Contao\Workflow\Model\State;

/**
 * Class Workflow stores all information of a step processing workflow.
 *
 * @package Netzmacht\Contao\Workflow\Flow
 */
class Workflow extends Configurable
{
    /**
     * Transitions being available in the workflow.
     *
     * @var Transition[]
     */
    private $transitions = array();

    /**
     * Steps being available in the workflow.
     *
     * @var Step[]
     */
    private $steps = array();

    /**
     * The start transition.
     *
     * @var Transition
     */
    private $startTransition;

    /**
     * Condition to match if workflow can handle an entity.
     *
     * @var Condition
     */
    private $condition;

    /**
     * Construct.
     *
     * @param string    $name                The name of the workflow.
     * @param array     $steps               Set of steps.
     * @param array     $transitions         Set of transitions.
     * @param string    $startTransitionName Name of the start transition.
     * @param Condition $condition           Optional pass a condition.
     * @param null      $label               The label of the workflow.
     * @param array     $config              Extra config.
     *
     * @throws TransitionNotFoundException
     */
    public function __construct(
        $name,
        array $steps,
        array $transitions,
        $startTransitionName,
        Condition $condition = null,
        $label = null,
        array $config = array()
    ) {
        Assertion::allIsInstanceOf($steps, 'Netzmacht\Contao\Workflow\Flow\Step');
        Assertion::allIsInstanceOf($transitions, 'Netzmacht\Contao\Workflow\Flow\Transition');

        parent::__construct($name, $label, $config);

        $this->transitions     = $transitions;
        $this->steps           = $steps;
        $this->startTransition = $this->getTransition($startTransitionName);
        $this->condition       = $condition;
    }

    /**
     * Get a transition by name.
     *
     * @param string $transitionName The name of the transition.
     *
     * @throws TransitionNotFoundException If transition is not found.
     *
     * @return \Netzmacht\Contao\Workflow\Flow\Transition If transition is not found.
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
     * Get a step by step name.
     *
     * @param string $stepName The step name.
     *
     * @return Step
     *
     * @throws StepNotFoundException If step is not found.
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
     * Get the start transition.
     *
     * @return Transition
     */
    public function getStartTransition()
    {
        return $this->startTransition;
    }

    /**
     * Consider if workflow is responsible for the entity.
     *
     * @param Entity $entity The entity.
     *
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
     * Transit the entity to a new state.
     *
     * @param Item    $item         The entity.
     * @param string  $transitionName The transition name.
     * @param Context $context        The context of the transition.
     *
     * @throws ProcessNotStartedException    If process was not started.
     * @throws StepNotFoundException         If step is not found.
     * @throws TransitionNotAllowedException If transition is not allowed.
     * @throws TransitionNotFoundException   If transition is not found.
     *
     * @return State
     */
    public function transit(Item $item, $transitionName, Context $context)
    {
        $this->guardWorkflowStarted($item);

        $currentStep = $this->getStep($item->getCurrentStepName());

        $this->guardTransitionAllowed($currentStep, $transitionName);

        $transition = $this->getTransition($transitionName);

        return $transition->transit($item, $context);
    }

    /**
     * Start a workflow.
     *
     * If the workflow is already started, nothing happens.
     *
     * @param Item    $item    The entity.
     * @param Context $context The transition context.
     *
     * @return State
     */
    public function start(Item $item, Context $context)
    {
        if ($item->isWorkflowStarted()) {
            return $item->getLatestState();
        }

        $transition = $this->getStartTransition();

        return $transition->transit($item, $context);
    }

    /**
     * Guard that workflow has already started.
     *
     * @param Item $item The entity.
     *
     * @throws ProcessNotStartedException If workflow has not started yet.
     *
     * @return void
     */
    private function guardWorkflowStarted(Item $item)
    {
        if (!$item->isWorkflowStarted()) {
            throw new ProcessNotStartedException();
        }
    }

    /**
     * Guard that transition is allowed.
     *
     * @param Step   $currentStep    The current step.
     * @param string $transitionName The name of the transition.
     *
     * @throws TransitionNotAllowedException If transition is not allowed.
     *
     * @return void
     */
    private function guardTransitionAllowed(Step $currentStep, $transitionName)
    {
        if (!$currentStep->isTransitionAllowed($transitionName)) {
            throw new TransitionNotAllowedException($currentStep->getName(), $transitionName);
        }
    }
}
