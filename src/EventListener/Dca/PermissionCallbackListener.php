<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\DataContainer;
use Contao\Model\Collection;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\Workflow\Flow\Security\Permission as WorkflowPermission;

use function assert;

/**
 * Class Permission initialize permission fields for the workflows for frontend and backend users.
 */
final class PermissionCallbackListener
{
    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Get all permissions.
     *
     * @return array<string,array<string,string>>
     */
    public function getAllPermissions(): array
    {
        $options    = [];
        $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $collection = $repository->findAll();

        if ($collection) {
            assert($collection instanceof Collection);

            foreach ($collection as $workflow) {
                $permissions = StringUtil::deserialize($workflow->permissions, true);

                foreach ($permissions as $permission) {
                    $workflowName = $workflow->label
                        ? ($workflow->label . ' [' . $workflow->id . ']')
                        : $workflow->id;

                    $name                          = 'workflow_' . $workflow->id . ':' . $permission['name'];
                    $options[$workflowName][$name] = $permission['label'] ?: $permission['name'];
                }
            }
        }

        return $options;
    }

    /**
     * Get all permissions of a specific workflow.
     *
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return array<string,string>
     */
    public function getWorkflowPermissions(DataContainer $dataContainer): array
    {
        if (! $dataContainer->activeRecord || ! $dataContainer->activeRecord->pid) {
            return [];
        }

        $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $workflow   = $repository->find((int) $dataContainer->activeRecord->pid);
        $options    = [];

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
