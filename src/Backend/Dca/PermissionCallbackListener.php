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

use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Workflow\Model\Workflow\WorkflowModel;
use Netzmacht\Workflow\Security\Permission as WorkflowPermission;

/**
 * Class Permission initialize permission fields for the workflows for frontend and backend users.
 *
 * @package Netzmacht\Contao\Workflow\Dca\Table
 */
class PermissionCallbackListener
{
    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Permission constructor.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Get all permissions.
     *
     * @return array
     */
    public function getAllPermissions()
    {
        $options    = array();
        $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $collection = $repository->findAll();

        if ($collection) {
            foreach ($collection as $workflow) {
                $permissions = StringUtil::deserialize($workflow->permissions, true);

                foreach ($permissions as $permission) {
                    $workflow = $workflow->label
                        ? ($workflow->label . ' [' . $workflow->name . ']')
                        : $workflow->name;

                    $name                      = $workflow->name . ':' . $permission['name'];
                    $options[$workflow][$name] = $permission['label'] ?: $permission['name'];
                }
            }
        }

        return $options;
    }

    /**
     * Get all permissions of a specific workflow.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     */
    public function getWorkflowPermissions($dataContainer)
    {
        if (!$dataContainer->activeRecord || !$dataContainer->activeRecord->pid) {
            return array();
        }

        $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $workflow   = $repository->find((int) $dataContainer->activeRecord->pid);
        $options    = [];

        if ($workflow) {
            $permissions = StringUtil::deserialize($workflow->permissions, true);

            foreach ($permissions as $config) {
                $permission = WorkflowPermission::forWorkflowName(
                    (string) $workflow->name,
                    (string) $config['name']
                );

                $options[(string) $permission] = $config['label'] ?: $config['name'];
            }
        }

        return $options;
    }
}
