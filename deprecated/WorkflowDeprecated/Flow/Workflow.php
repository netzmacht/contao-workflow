<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Flow;

use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBag;
use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Netzmacht\Contao\WorkflowDeprecated\Exception\InvalidArgumentException;


class Workflow
{
    /**
     * @var Condition
     */
    private $condition;

    /**
     * @var Step[]
     */
    private $steps = array();

    /**
     * @var
     */
    private $name;

    /**
     * @var Transition
     */
    private $startTransition;

    /**
     * @param $entity
     * @return bool
     */
    public function match(Entity $entity)
    {
        if ($this->condition) {
            return $this->condition->match($entity);
        }

        return true;
    }

    /**
     * @param Entity $entity
     * @return \Netzmacht\Contao\WorkflowDeprecated\Flow\State
     */
    public function start(Entity $entity, PropertyValueBag $values)
    {
        $this->guardHasStartTransition();
        $this->guardNoWorkflowStateGiven($entity);

        return $this->transit($entity, $this->startTransition, $values);
    }

    /**
     * @param Entity $entity
     * @param Transition $transition
     * @param PropertyValueBag $values
     * @throws InvalidArgumentException
     * @return \Netzmacht\Contao\WorkflowDeprecated\Flow\Step
     */
    public function transit(Entity $entity, Transition $transition, PropertyValueBag $values)
    {
        $this->guardResponsibilityForEntity($entity);

        $state = $transition->transit($entity, $values);

        return $state;
    }

    /**
     * @param Entity $entity
     * @return Step
     */
    public function getCurrentStep(Entity $entity)
    {
        $data = $entity->getWorkflowState();
        $step = $this->getStep($data->getStepName());

        return $step;
    }

    /**
     * @param $stepName
     * @throws InvalidArgumentException
     * @return Step
     */
    private function getStep($stepName)
    {
        foreach ($this->steps as $step) {
            if($step->getName() == $stepName) {
                return $step;
            }
        }

        throw new InvalidArgumentException(
            sprintf('Step "%s" does not belong to workflow "%s"', $stepName, $this->name)
        );
    }

    /**
     * @param Entity $entity
     * @throws InvalidArgumentException
     */
    public function guardResponsibilityForEntity(Entity $entity)
    {
        if (!$this->match($entity)) {
            throw new InvalidArgumentException('Workflow is not responsible for entity');
        }
    }

    /**
     * @return Step
     */
    public function getStartStep()
    {
    }

    /**
     * @param $transitionName
     * @return Transition
     */
    public function getTransition($transitionName)
    {
    }

    /**
     * @param Entity $entity
     */
    private function guardNoWorkflowStateGiven(Entity $entity)
    {
        if($entity->getWorkflowState()) {
            throw new \RuntimeException('Workflow of entity already started');
        }
    }

    /**
     *
     */
    private function guardHasStartTransition()
    {
        if(!$this->startTransition) {
            throw new \RuntimeException('No start transition defined for workflow');
        }
    }

} 