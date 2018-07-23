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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionFactory;
use Netzmacht\ContaoWorkflowBundle\Workflow\Manager\Manager;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;

/**
 * Class Action is used for tl_workflow_action callbacks.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Dca
 */
final class ActionCallbackListener
{
    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Workflow manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * The action factory.
     *
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * Action constructor.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param Manager           $manager           Workflow manager.
     * @param ActionFactory     $actionFactory     The action factory.
     */
    public function __construct(
        RepositoryManager $repositoryManager,
        Manager $manager,
        ActionFactory $actionFactory
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->manager           = $manager;
        $this->actionFactory     = $actionFactory;
    }

    /**
     * Get all available types.
     *
     * @param DataContainer $dataContainer The data container.
     *
     * @return array
     */
    public function getTypes($dataContainer): array
    {
        $transition = $this->repositoryManager
            ->getRepository(TransitionModel::class)
            ->find((int) $dataContainer->activeRecord->pid);

        if (!$transition) {
            return [];
        }

        $workflow = $this->manager->getWorkflowById((int) $transition->pid);

        return $this->actionFactory->getSupportedTypeNames($workflow, true);
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
