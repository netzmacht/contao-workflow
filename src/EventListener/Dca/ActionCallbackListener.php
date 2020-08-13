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
use Contao\Input;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\ContaoWorkflowBundle\Model\Action\ActionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader\DatabaseDrivenWorkflowLoader;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionFactory;
use NotificationCenter\Model\Notification;
use function end;
use function implode;
use function sprintf;

/**
 * Class Action is used for tl_workflow_action callbacks.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Dca
 */
final class ActionCallbackListener extends AbstractListener
{
    use EntityPropertiesTrait;

    /**
     * The data container name.
     *
     * @var string
     */
    protected static $name = 'tl_workflow_action';

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
     * @param DcaManager                   $dcaManager        Data container manager.
     * @param RepositoryManager            $repositoryManager Repository manager.
     * @param DatabaseDrivenWorkflowLoader $workflowLoader    Database driven workflow loader.
     * @param ActionFactory                $actionFactory     The action factory.
     */
    public function __construct(
        DcaManager $dcaManager,
        RepositoryManager $repositoryManager,
        DatabaseDrivenWorkflowLoader $workflowLoader,
        ActionFactory $actionFactory
    ) {
        parent::__construct($dcaManager);

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

    /**
     * Get entity properties.
     *
     * @return array
     */
    public function getEntityProperties(): array
    {
        $action = $this->repositoryManager->getRepository(ActionModel::class)->find((int) Input::get('id'));
        if (! $action) {
            return [];
        }

        $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $workflow   = $repository->find((int) $action->pid);
        if (!$workflow instanceof WorkflowModel) {
            return [];
        }

        return $this->getEntityPropertiesForWorkflow($workflow);
    }

    /**
     * Get entity properties.
     *
     * @return array
     */
    public function getEditableEntityProperties(): array
    {
        $action = $this->repositoryManager->getRepository(ActionModel::class)->find((int) Input::get('id'));
        if (! $action) {
            return [];
        }

        $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $workflow   = $repository->find((int) $action->pid);
        if (!$workflow instanceof WorkflowModel) {
            return [];
        }

        $definition = $this->getDefinition($workflow->providerName);
        $options    = [];
        foreach ($definition->get('fields') as $name => $config) {
            if (!isset($config['inputType'])) {
                continue;
            }

            $options[$name] = isset($config['label'][0]) ? sprintf('%s [%s]', $config['label'][0], $name) : $name;
        }

        return $options;
    }
}
