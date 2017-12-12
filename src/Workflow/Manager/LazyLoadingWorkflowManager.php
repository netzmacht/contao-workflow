<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Manager;

use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader\WorkflowLoader;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\Entity;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Manager\Manager as BaseManager;

/**
 * Class LazyLoadingWorkflowManager.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Manager
 */
class LazyLoadingWorkflowManager implements Manager
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
     * LazyLoadingWorkflowManager constructor.
     *
     * @param Manager        $inner          Decorated workflow manager.
     * @param WorkflowLoader $workflowLoader The workflow definition loader.
     */
    public function __construct(Manager $inner, WorkflowLoader $workflowLoader)
    {
        $this->inner          = $inner;
        $this->workflowLoader = $workflowLoader;
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkflowById(int $workflowId): Workflow
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->getWorkflowById($workflowId);
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Item $item, string $transitionName = null, bool $changeWorkflow = false): ?TransitionHandler
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->handle($item, $transitionName, $changeWorkflow);
    }

    /**
     * {@inheritDoc}
     */
    public function addWorkflow(Workflow $workflow): BaseManager
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

    /**
     * {@inheritDoc}
     */
    public function getWorkflowByName(string $name): Workflow
    {
        $this->loadWorkflowDefinitions();

        return $this->inner->getWorkflowByName($name);
    }

    /**
     * {@inheritDoc}
     */
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
     * {@inheritDoc}
     */
    public function createEntity(EntityId $entityId, $model): Entity
    {
        return $this->inner->createEntity($entityId, $model);
    }

    /**
     * Load all workflow definitions.
     *
     * @return void
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
