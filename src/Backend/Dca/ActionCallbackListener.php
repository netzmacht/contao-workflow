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

namespace Netzmacht\Contao\Workflow\Backend\Dca;

use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Workflow\Action\ActionFactory;
use Netzmacht\Contao\Workflow\Backend\Event\GetWorkflowActionsEvent;
use Netzmacht\Contao\Workflow\Model\RoleModel;
use Netzmacht\Contao\Workflow\Model\TransitionModel;
use Netzmacht\Contao\Workflow\Model\WorkflowModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Action is used for tl_workflow_action callbacks.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class ActionCallbackListener
{
    /**
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * Action constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param RepositoryManager        $repositoryManager
     * @param ActionFactory            $actionFactory
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RepositoryManager $repositoryManager,
        ActionFactory $actionFactory
    ) {
        $this->eventDispatcher   = $eventDispatcher;
        $this->repositoryManager = $repositoryManager;
        $this->actionFactory     = $actionFactory;
    }

    /**
     * Get all available types.
     *
     * @param \DataContainer $dataContainer The data container.
     *
     * @return array
     */
    public function getTypes($dataContainer): array
    {
        $workflowModel = $this->getWorkflowModel($dataContainer);
        $event         = new GetWorkflowActionsEvent($workflowModel);

        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->getActions();
    }

    /**
     * Get workflow roles.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     */
    public function getRoles($dataContainer): array
    {
        $workflowModel = $this->getWorkflowModel($dataContainer);
        $repository    = $this->repositoryManager->getRepository(RoleModel::class);
        $collection    = $repository->findBy(['.pid=?'], [$workflowModel->id], ['order' => 'label']);
        $options       = [];

        while ($collection && $collection->next()) {
            $options[$collection->id] = $collection->label;
        }

        return $options;
    }

    /**
     * Get the workflow model.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return WorkflowModel|null
     *
     * @throws \Exception If relation could not be resolved.
     */
    protected function getWorkflowModel($dataContainer):? WorkflowModel
    {
        $repository      = $this->repositoryManager->getRepository(TransitionModel::class);
        $transitionModel = $repository->find((int) $dataContainer->activeRecord->pid);
        $workflowModel   = $transitionModel->getRelated('pid');

        return $workflowModel;
    }
}
