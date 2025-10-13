<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Manager;

use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader\WorkflowLoader;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Manager\Manager;
use Override;

final readonly class LazyLoadingWorkflowManager implements Manager
{
    /**
     * @param Manager        $inner          Decorated workflow manager.
     * @param WorkflowLoader $workflowLoader The workflow definition loader.
     */
    public function __construct(private Manager $inner, private WorkflowLoader $workflowLoader)
    {
    }

    #[Override]
    public function handle(
        Item $item,
        string|null $transitionName = null,
        bool $changeWorkflow = false,
    ): TransitionHandler|null {
        $this->loadWorkflowDefinitions();

        return $this->inner->handle($item, $transitionName, $changeWorkflow);
    }

    #[Override]
    public function addWorkflow(Workflow $workflow): Manager
    {
        $this->inner->addWorkflow($workflow);

        return $this;
    }

    /** {@inheritDoc} */
    #[Override]
    public function getWorkflow(EntityId $entityId, $entity): Workflow
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->getWorkflow($entityId, $entity);
    }

    #[Override]
    public function getWorkflowByName(string $name): Workflow
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->getWorkflowByName($name);
    }

    #[Override]
    public function getWorkflowByItem(Item $item): Workflow
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->getWorkflowByItem($item);
    }

    /** {@inheritDoc} */
    #[Override]
    public function hasWorkflow(EntityId $entityId, $entity): bool
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->hasWorkflow($entityId, $entity);
    }

    /** {@inheritDoc} */
    #[Override]
    public function getWorkflows(): iterable
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->getWorkflows();
    }

    /** {@inheritDoc} */
    #[Override]
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
