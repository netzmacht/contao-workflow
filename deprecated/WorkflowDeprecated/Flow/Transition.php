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
use Netzmacht\Contao\WorkflowDeprecated\Action;
use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Netzmacht\Contao\WorkflowDeprecated\Exception\InvalidTransitionException;
use Netzmacht\Contao\WorkflowDeprecated\View;
use Netzmacht\Contao\WorkflowDeprecated\View\FormFactory;

class Transition
{
    private $name;

    /**
     * @var Action[]
     */
    private $actions;

    /**
     * @var Step
     */
    private $fromStep;

    /**
     * @var Step
     */
    private $toStep;

    /**
     * @param Entity $entity
     * @return bool
     */
    public function validate(Entity $entity)
    {
        foreach ($this->actions as $action) {
            if (!$action->validate($this, $entity)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasForm()
    {
        foreach ($this->actions as $action) {
            if ($action->hasForm()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Entity $entity
     * @throws InvalidTransitionException
     * @return State
     */
    public function transit(Entity $entity, PropertyValueBag $propertyValues)
    {
        $currentStep = $entity->getWorkflowState()->getStepName();
        $this->gardValidTransition($currentStep);

        $success   = $this->validate($entity);
        $stateData = new PropertyValueBag();

        if ($success) {
            foreach ($this->actions as $action) {
                $action->execute($this, $entity, $stateData);
            }
        }

        $workflowData = $entity->getWorkflowState();
        $workflowData->transit($entity, $this, $stateData->getArrayCopy(), $success);

        return $success;
    }

    /**
     * @param Step $currentStep
     * @throws InvalidTransitionException
     */
    private function gardValidTransition($currentStep)
    {
        if ($this->fromStep->getName() != $currentStep) {
            throw new InvalidTransitionException('Transition "%s" is not allowed for current step "%s"');
        }
    }

    /**
     * @return string
     */
    public function getTarget()
    {
    }

    /**
     * @param FormFactory $formFactory
     */
    public function buildForm(FormFactory $formFactory)
    {
        foreach ($this->actions as $action) {
            $action->buildForm($formFactory);
        }
    }

} 