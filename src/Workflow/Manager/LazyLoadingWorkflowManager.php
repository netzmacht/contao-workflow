<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Manager;

use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader\WorkflowLoader;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Manager\Manager;

final class LazyLoadingWorkflowManager implements Manager
{
    /**
     * Workflow manager.
     *
     * @var Manager
     */
    private $inner;

    /**
     * The workflow definition loader.
     *
     * @var WorkflowLoader
     */
    private $workflowLoader;

    /**
     * @param Manager        $inner          Decorated workflow manager.
     * @param WorkflowLoader $workflowLoader The workflow definition loader.
     */
    public function __construct(Manager $inner, WorkflowLoader $workflowLoader)
    {
        $this->inner          = $inner;
        $this->workflowLoader = $workflowLoader;
    }

    public function handle(Item $item, ?string $transitionName = null, bool $changeWorkflow = false): ?TransitionHandler
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->handle($item, $transitionName, $changeWorkflow);
    }

    public function addWorkflow(Workflow $workflow): Manager
    {
        $this->inner->addWorkflow($workflow);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkflow(EntityId $entityId, $entity): Workflow
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->getWorkflow($entityId, $entity);
    }

    public function getWorkflowByName(string $name): Workflow
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->getWorkflowByName($name);
    }

    public function getWorkflowByItem(Item $item): Workflow
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->getWorkflowByItem($item);
    }

    /**
     * {@inheritDoc}
     */
    public function hasWorkflow(EntityId $entityId, $entity): bool
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->hasWorkflow($entityId, $entity);
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkflows(): iterable
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->getWorkflows();
    }

    /**
     * {@inheritDoc}
     */
    public function createItem(EntityId $entityId, $entity): Item
    {
        return $this->inner->createItem($entityId, $entity);
    }

    /**
     * Load all workflow definitions.
     */
    private function loadWorkflowDefinitions(): void
    {
        static $loaded = false;

        if ($loaded) {
            return;
        }

        $loaded = true;

        foreach ($this->workflowLoader->load() as $workflow) {
            $this->addWorkflow($workflow);
        }
    }
}
