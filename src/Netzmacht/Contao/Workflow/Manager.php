<?php

namespace Netzmacht\Contao\Workflow;

use Assert\Assertion;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Contao\Workflow\Exception\Flow\WorkflowException;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\TransitionHandler\TransitionHandlerFactory;

/**
 * Class Manager handles a set of workflows.
 *
 * Usually there will a different workflow manager for different workflow types. The manager is the API entry point
 * when using the workflow API.
 *
 * @package Netzmacht\Contao\Workflow
 */
class Manager
{
    /**
     * The state repository.
     *
     * @var StateRepository
     */
    private $stateRepository;

    /**
     * A set of workflows.
     *
     * @var Workflow[]
     */
    private $workflows;

    /**
     * A Transition handler factory.
     *
     * @var TransitionHandlerFactory
     */
    private $handlerFactory;

    /**
     * Construct.
     *
     * @param TransitionHandlerFactory $handlerFactory
     * @param StateRepository          $stateRepository
     * @param Workflow[]|array         $workflows The set of managed workflows.
     */
    public function __construct(
        TransitionHandlerFactory $handlerFactory,
        StateRepository $stateRepository,
        array $workflows = array()
    ) {
        Assertion::allIsInstanceOf($workflows, 'Netzmacht\Contao\Workflow\Flow\Workflow');

        $this->workflows       = $workflows;
        $this->handlerFactory  = $handlerFactory;
        $this->stateRepository = $stateRepository;
    }

    /**
     * Handle a workflow transition of an entity will createRepository a transition handler.
     *
     * If no matching workflow definition is found false will be returned.
     *
     * @param Item   $item
     * @param string $transitionName Transition name, required if workflow has already started.
     *
     * @throws WorkflowException
     *
     * @return bool|TransitionHandler
     */
    public function handle(Item $item, $transitionName = null)
    {
        $entity   = $item->getEntity();
        $workflow = $this->getWorkflow($entity);

        if (!$workflow) {
            return false;
        }

        $this->guardSameWorkflow($item, $workflow);

        $handler = $this->handlerFactory->createTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $entity->getProviderName(),
            $this->stateRepository
        );

        return $handler;
    }


    /**
     * Add a workflow to the manager.
     *
     * @param Workflow $workflow The workflow being added.
     *
     * @return $this
     */
    public function addWorkflow(Workflow $workflow)
    {
        $this->workflows[] = $workflow;

        return $this;
    }

    /**
     * Get a workflow for the given entity.
     *
     * @param Entity $entity The entity.
     *
     * @return Workflow|bool
     */
    public function getWorkflow(Entity $entity)
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->match($entity)) {
                return $workflow;
            }
        }

        return false;
    }

    /**
     * Get Workflow by its name.
     *
     * @param string $name Name of workflow.
     *
     * @return bool|Workflow
     */
    public function getWorkflowByName($name)
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->getName() == $name) {
                return $workflow;
            }
        }

        return false;
    }

    /**
     * Consider if entity has an workflow.
     *
     * @param Entity $entity The entity.
     *
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
     * Get all registered workflows.
     *
     * @return Workflow[]
     */
    public function getWorkflows()
    {
        return $this->workflows;
    }

    /**
     * @param Entity $entity
     *
     * @return Item
     */
    public function createItem(Entity $entity)
    {
        $stateHistory = $this->stateRepository->find($entity->getProviderName(), $entity->getId());

        return Item::restore($entity, $stateHistory);
    }

    /**
     * Guard that already started workflow is the same which is tried to be runned now.
     *
     * @param Item     $item     Current workflow item.
     * @param Workflow $workflow Selected workflow.
     *
     * @throws WorkflowException
     */
    private function guardSameWorkflow(Item $item, Workflow $workflow)
    {
        if ($item->isWorkflowStarted() && $item->getWorkflowName() != $workflow->getName()) {
            $message = sprintf(
                'Item %s::%s already process workflow "%s" and cannot be handled by "%s"',
                $item->getEntity()->getProviderName(),
                $item->getEntity()->getId(),
                $item->getWorkflowName(),
                $workflow->getName()
            );

            throw new WorkflowException($message);
        }
    }
}
