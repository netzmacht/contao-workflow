<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated;


use Netzmacht\Contao\WorkflowDeprecated\Exception\InvalidArgumentException;
use Netzmacht\Contao\WorkflowDeprecated\Flow\Workflow;
use Netzmacht\Contao\WorkflowDeprecated\Model\WorkflowDataRepository;

class Manager
{
    /**
     * @var Workflow[]
     */
    private $workflows = array();

    /**
     * @var WorkflowDataRepository
     */
    private $workflowDataRepository;

    /**
     * @var InputProvider
     */
    private $inputProvider;

    /**
     * @param Workflow[] $workflows
     * @param WorkflowDataRepository $workflowDataRepository
     * @param InputProvider $inputProvider
     */
    function __construct(array $workflows, WorkflowDataRepository $workflowDataRepository, InputProvider $inputProvider)
    {
        $this->workflowDataRepository = $workflowDataRepository;
        $this->workflows              = $workflows;
        $this->inputProvider          = $inputProvider;
    }


    /**
     * @param Entity $entity
     * @param $transitionName
     * @param View $view
     * @throws Exception\NoWorkflowDataGivenException
     * @throws InvalidArgumentException
     * @return \Netzmacht\Contao\WorkflowDeprecated\Flow\Step
     */
    public function transit(Entity $entity, $transitionName, View $view)
    {
        $workflow = $this->getWorkflow($entity);
        $data     = $this->loadWorkflowData($entity, $workflow);

        $transition = $workflow->getTransition($transitionName);
        $transition->fill($this->inputProvider);

        if($transition->hasFormView() && !$transition->validate($entity)) {
            $transition->buildForm($view);
            $view->forceDisplay();

            return $workflow->getCurrentStep($entity);
        }

        $step    = $workflow->transit($entity, $transition);
        $newData = $entity->getWorkflowState();

        if ($data != $newData) {
            $this->workflowDataRepository->add($newData);
        }

        return $step;
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function hasWorkflow(Entity $entity)
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->match($entity)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Entity $entity
     * @return Workflow
     * @throws InvalidArgumentException
     */
    public function getWorkflow(Entity $entity)
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->match($entity)) {
               return $workflow;
            }
        }

        throw new InvalidArgumentException('No workflow found for entity');
    }

    /**
     * @param Entity $entity
     * @return \Netzmacht\Contao\WorkflowDeprecated\WorkflowData|static
     */
    private function loadWorkflowData(Entity $entity, Workflow $workflow)
    {
        $workflowData = $this->workflowDataRepository->findLatest($entity);

        if (!$workflowData) {
            $workflowData = WorkflowData::initiate($entity, $workflow);
            $this->workflowDataRepository->add($workflowData);
        }

        return $workflowData;
    }
} 