<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\ContaoWorkflowBundle\Model\Action\ActionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Action\ActionRepository;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader\DatabaseDrivenWorkflowLoader;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionFactory;
use Netzmacht\Workflow\Flow\Security\Permission as WorkflowPermission;
use NotificationCenter\Model\Notification;
use function assert;
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
     * Provider configuration.
     *
     * @var array
     */
    private $providerConfiguration;

    /**
     * Action constructor.
     *
     * @param DcaManager                   $dcaManager            Data container manager.
     * @param RepositoryManager            $repositoryManager     Repository manager.
     * @param DatabaseDrivenWorkflowLoader $workflowLoader        Database driven workflow loader.
     * @param ActionFactory                $actionFactory         The action factory.
     * @param array                        $providerConfiguration Provider configuration.
     */
    public function __construct(
        DcaManager $dcaManager,
        RepositoryManager $repositoryManager,
        DatabaseDrivenWorkflowLoader $workflowLoader,
        ActionFactory $actionFactory,
        array $providerConfiguration
    ) {
        parent::__construct($dcaManager);

        $this->repositoryManager     = $repositoryManager;
        $this->workflowLoader        = $workflowLoader;
        $this->actionFactory         = $actionFactory;
        $this->providerConfiguration = $providerConfiguration;
    }

    /**
     * Generate a row view.
     *
     * @param array $row Current data row.
     *
     * @return string
     */
    public function generateRow(array $row): string
    {
        if ($row['type'] === 'reference') {
            $reference = $this->repositoryManager->getRepository(ActionModel::class)->find((int) $row['reference']);
            if ($reference === null) {
                return sprintf('<strong>ID %s</strong>', $row['id']);
            }
            $row = $reference->row();
        }

        return sprintf(
            '<strong>%s</strong><br>%s',
            $row['label'],
            $row['description']
        );
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

        if ($dataContainer->activeRecord->ptable === 'tl_workflow') {
            $workflow = $this->workflowLoader->loadWorkflowById((int) $dataContainer->activeRecord->pid);
            $actions  = [];
        } else {
            $repository      = $this->repositoryManager->getRepository(TransitionModel::class);
            $transitionModel = $repository->find((int) $dataContainer->activeRecord->pid);
            $workflow        = $this->workflowLoader->loadWorkflowById((int) $transitionModel->pid);
            $actions         = ['transitions' => ['reference']];
        }

        return array_merge($actions, $this->actionFactory->getSupportedTypeNamesCategorized($workflow));
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
        $workflow = $this->getWorkflowModelByAction();
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
        $workflow = $this->getWorkflowModelByAction();
        if (!$workflow instanceof WorkflowModel) {
            return [];
        }

        $definition = $this->getDefinition($workflow->providerName);
        $options    = [];
        foreach ($definition->get('fields') as $name => $config) {
            if (!isset($config['inputType'])) {
                continue;
            }

            $options[$name] = isset($config['label'][0])
                ? sprintf(
                    '%s <span style="display:inline-block;" class="tl_gray">[%s]</span>',
                    $config['label'][0],
                    $name
                )
                : $name;
        }

        return $options;
    }

    /**
     * Get actions defined as global actions.
     *
     * @return array
     */
    public function getWorkflowActions(): array
    {
        $workflow = $this->getWorkflowModelByAction();
        if (!$workflow instanceof WorkflowModel) {
            return [];
        }

        $repository = $this->repositoryManager->getRepository(ActionModel::class);
        assert($repository instanceof ActionRepository);
        $collection = $repository->findByWorkflow((int) $workflow->id) ?: [];
        $options    = [];
        foreach ($collection as $model) {
            $options[$model->id] = $model->label;
        }

        return $options;
    }

    /**
     * Get the related workflow model by the action.
     *
     * @return WorkflowModel|null
     */
    private function getWorkflowModelByAction(): ?WorkflowModel
    {
        $action = $this->repositoryManager->getRepository(ActionModel::class)->find((int) Input::get('id'));
        if (!$action) {
            return null;
        }

        switch ($action->ptable) {
            case 'tl_workflow':
                $workflowId = (int) $action->pid;
                break;

            case 'tl_workflow_transition':
                $transitionRepository = $this->repositoryManager->getRepository(TransitionModel::class);
                $transitionModel      = $transitionRepository->find((int) $action->pid);
                if ($transitionModel === null) {
                    return null;
                }
                $workflowId = (int) $transitionModel->pid;
                break;

            default:
                return null;
        }

        $repository    = $this->repositoryManager->getRepository(WorkflowModel::class);
        $workflowModel = $repository->find($workflowId);

        if ($workflowModel instanceof WorkflowModel) {
            return $workflowModel;
        }

        return null;
    }

    /**
     * Get user assign properties.
     *
     * @return array
     */
    public function getUserAssignProperties(): array
    {
        $workflow = $this->getWorkflowModelByAction();
        if ($workflow === null) {
            return [];
        }

        return ($this->providerConfiguration[$workflow->providerName]['assign_users'] ?? []);
    }

    /**
     * Get workflow permissions.
     *
     * @return array
     */
    public function getWorkflowPermissions(): array
    {
        $workflow = $this->getWorkflowModelByAction();
        $options  = [];

        if ($workflow) {
            $permissions = StringUtil::deserialize($workflow->permissions, true);

            foreach ($permissions as $config) {
                $permission = WorkflowPermission::forWorkflowName(
                    'workflow_' . $workflow->id,
                    (string) $config['name']
                );

                $options[(string) $permission] = $config['label'] ?: $config['name'];
            }
        }

        return $options;
    }
}
