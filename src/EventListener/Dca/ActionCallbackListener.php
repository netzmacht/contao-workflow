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
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader\DatabaseDrivenWorkflowLoader;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionFactory;
use NotificationCenter\Model\Notification;

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
     * Workflow definition loader.
     *
     * @var DatabaseDrivenWorkflowLoader
     */
    private $workflowLoader;

    /**
     * The action factory.
     *
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * Action constructor.
     *
     * @param RepositoryManager            $repositoryManager Repository manager.
     * @param DatabaseDrivenWorkflowLoader $workflowLoader    Database driven workflow loader.
     * @param ActionFactory                $actionFactory     The action factory.
     */
    public function __construct(
        RepositoryManager $repositoryManager,
        DatabaseDrivenWorkflowLoader $workflowLoader,
        ActionFactory $actionFactory
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->workflowLoader    = $workflowLoader;
        $this->actionFactory     = $actionFactory;
    }

    /**
     * Get all available types.
     *
     * @param DataContainer $dataContainer The data container.
     *
     * @return array
     */
    public function getTypes($dataContainer = null): array
    {
        if ($dataContainer === null || $dataContainer->activeRecord === null) {
            return $this->actionFactory->getTypeNames();
        }

        $workflow = $this->workflowLoader->loadWorkflowById((int) $dataContainer->activeRecord->pid);

        return $this->actionFactory->getSupportedTypeNamesCategorized($workflow);
    }

    /**
     * Get all notifications as options.
     *
     * @return array
     */
    public function notificationOptions(): array
    {
        $notifications = $this->repositoryManager
            ->getRepository(Notification::class)
            ->findBy(['.type=?'], ['workflow_transition'], ['order' => '.title']);

        return OptionsBuilder::fromCollection(
            $notifications,
            function (array $row) {
                return sprintf('%s [ID %s]', $row['title'], $row['id']);
            }
        )->getOptions();
    }
}
