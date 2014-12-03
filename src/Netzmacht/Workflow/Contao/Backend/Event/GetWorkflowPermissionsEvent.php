<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Backend\Event;


use Netzmacht\Workflow\Contao\Model\WorkflowModel;
use Netzmacht\Workflow\Security\Permission;
use Symfony\Component\EventDispatcher\Event;

class GetWorkflowPermissionsEvent extends Event
{
    const NAME = 'workflow.get-workflow-permissions';

    /**
     * @var WorkflowModel
     */
    private $workflowModel;

    /**
     * @var Permission[]
     */
    private $permissions = array();

    /**
     * @param $workflowModel
     */
    public function __construct(WorkflowModel $workflowModel)
    {
        $this->workflowModel = $workflowModel;
    }

    /**
     * @return WorkflowModel
     */
    public function getWorkflowModel()
    {
        return $this->workflowModel;
    }

    /**
     * @param $permissionId
     *
     * @return $this
     */
    public function addPermission($permissionId, $group)
    {
        $this->permissions[$group][] = Permission::forWorkflowName($this->workflowModel->name, $permissionId);

        return $this;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}
