<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Backend\Event;

use Netzmacht\Contao\Workflow\Model\WorkflowModel;
use Netzmacht\Workflow\Security\Permission;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GetWorkflowPermissionsEvent is emitted when workflow permissions are collected.
 *
 * @package Netzmacht\Contao\Workflow\Backend\Event
 */
class GetWorkflowPermissionsEvent extends Event
{
    const NAME = 'workflow.get-workflow-permissions';

    /**
     * Workflow model.
     *
     * @var WorkflowModel
     */
    private $workflowModel;

    /**
     * Permissions.
     *
     * @var Permission[]
     */
    private $permissions = array();

    /**
     * Construct.
     *
     * @param WorkflowModel $workflowModel The workflow model.
     */
    public function __construct(WorkflowModel $workflowModel)
    {
        $this->workflowModel = $workflowModel;
    }

    /**
     * Get the workflow model.
     *
     * @return WorkflowModel
     */
    public function getWorkflowModel()
    {
        return $this->workflowModel;
    }

    /**
     * Add a permission.
     *
     * @param string $permissionId The permission id.
     * @param string $group        Permission group.
     *
     * @return $this
     */
    public function addPermission($permissionId, $group)
    {
        $this->permissions[$group][] = Permission::forWorkflowName($this->workflowModel->name, $permissionId);

        return $this;
    }

    /**
     * Get all permissions.
     *
     * @return Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}
