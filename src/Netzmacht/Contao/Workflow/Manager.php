<?php

namespace Netzmacht\Contao\Workflow;

use Assert\Assertion;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface as InputProvider;
use Netzmacht\Contao\Workflow\Factory\RepositoryFactory;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\Transaction\TransactionHandler;

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
     * The repository factory.
     *
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * The transaction handler.
     *
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * The input provider.
     *
     * @var InputProvider
     */
    private $inputProvider;

    /**
     * Construct.
     *
     * @param StateRepository    $stateRepository    The state repository.
     * @param RepositoryFactory  $repositoryFactory  The entity repository factory.
     * @param TransactionHandler $transactionHandler The transaction handler.
     * @param InputProvider      $inputProvider      The input provider.
     * @param Workflow[]|array   $workflows          The set of managed workflows.
     */
    public function __construct(
        StateRepository $stateRepository,
        RepositoryFactory $repositoryFactory,
        TransactionHandler $transactionHandler,
        InputProvider $inputProvider,
        array $workflows = array()
    ) {
        Assertion::allIsInstanceOf($workflows, 'Netzmacht\Contao\Workflow\Flow\Workflow');

        $this->workflows          = $workflows;
        $this->stateRepository    = $stateRepository;
        $this->repositoryFactory  = $repositoryFactory;
        $this->transactionHandler = $transactionHandler;
        $this->inputProvider      = $inputProvider;
    }

    /**
     * Handle a workflow transition of an entity will createRepository a transition handler.
     *
     * If no matching workflow definition is found false will be returned.
     *
     * @param Entity $entity         The entity to transit through a workflow.
     * @param string $transitionName Transition name, required if workflow has already started.
     *
     * @return bool|\Netzmacht\Contao\Workflow\TransitionHandler
     */
    public function handle(Entity $entity, $transitionName = null)
    {
        $workflow = $this->getWorkflow($entity);

        if (!$workflow) {
            return false;
        }

        $handler = new TransitionHandler(
            $this->createItem($entity),
            $workflow,
            $transitionName,
            $this->repositoryFactory->createRepository($entity->getProviderName()),
            $this->stateRepository,
            $this->transactionHandler,
            new Context($this->inputProvider)
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
    private function createItem(Entity $entity)
    {
        $stateHistory = $this->stateRepository->find($entity->getProviderName(), $entity->getId());

        return Item::restore($entity, $stateHistory);
    }
}
