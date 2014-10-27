<?php

namespace Netzmacht\Contao\Workflow;

use Assert\Assertion;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface as InputProvider;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Entity\RepositoryFactory;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\Transaction\TransactionHandler;

class Manager
{
    /**
     * @var StateRepository
     */
    private $stateRepository;

    /**
     * @var Workflow[]
     */
    private $workflows;

    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * @var InputProvider
     */
    private $inputProvider;

    /**
     * @param StateRepository $stateRepository
     * @param RepositoryFactory $repositoryFactory
     * @param TransactionHandler $transactionHandler
     * @param Workflow[] $workflows
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
     * Handle a workflow transition of an entity will create a transition handler.
     * If no matching workflow definition is found false will be returned
     *
     * @param   Entity  $entity
     * @param   string  $transitionName Transition name, required if workflow has already started
     *
     * @return  bool|\Netzmacht\Contao\Workflow\TransitionHandler
     */
    public function handle(Entity $entity, $transitionName = null)
    {
        $workflow = $this->getWorkflow($entity);

        if (!$workflow) {
            return false;
        }

        if ($this->loadState($entity)) {
            Assertion::string($transitionName, 'Transition name is required');
        }

        $handler = new TransitionHandler(
            $entity,
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
     * @param Workflow $workflow
     *
     * @return $this
     */
    public function addWorkflow(Workflow $workflow)
    {
        $this->workflows[] = $workflow;

        return $this;
    }

    /**
     * @param Entity $entity
     *
     *@return Workflow|bool
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
     * @return Flow\Workflow[]
     */
    public function getWorkflows()
    {
        return $this->workflows;
    }

    /**
     * Initialize state of an entity
     *
     * @param Entity $entity
     * @return bool
     */
    private function loadState(Entity $entity)
    {
        if ($entity->getState()) {
            return true;
        }

        try {
            $state = $this->stateRepository->find($entity->getProviderName(), $entity->getId());
            $entity->transit($state);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
