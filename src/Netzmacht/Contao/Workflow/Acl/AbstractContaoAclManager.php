<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Acl;


use Netzmacht\Contao\Workflow\Contao\Model\RoleModel;
use Netzmacht\Contao\Workflow\Flow\Workflow;

abstract class AbstractContaoAclManager implements AclManager
{
    /**
     * @var \User
     */
    protected $user;

    /**
     * @param \User $user
     */
    public function __construct(\User $user)
    {
        $this->user = $user;
    }

    /**
     * @param Workflow $workflow
     * @param Role     $userRole
     *
     * @return bool
     */
    public function hasPermission(Workflow $workflow, Role $userRole)
    {
        $roles = $this->getRoles($workflow);

        foreach ($roles as $workflowRole) {
            if ($workflowRole->equals($userRole)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Workflow $workflow
     *
     * @return Role[]
     */
    public function getRoles(Workflow $workflow)
    {
        $roles      = array();
        $collection = RoleModel::findBy('pid', $workflow->getId());

        if ($collection) {
            while ($collection->next()) {
                if ($this->hasUserRole($collection->current())) {
                    $roles[] = new Role($collection->id, $collection->name);
                }
            }
        }

        return $roles;
    }

    /**
     * @param RoleModel $model
     *
     * @return bool
     */
    private function hasUserRole(RoleModel $model)
    {
        if ($this->matchUser($model)) {
            return true;
        }

        return $this->matchGroups($model);
    }

    /**
     * @param RoleModel $model
     *
     * @return bool
     */
    private function matchGroups(RoleModel $model)
    {
        if (array_intersect(deserialize($model->groups, true), $this->user->groups)) {
            return true;
        }

        return false;
    }

    /**
     * @param $model
     *
     * @return bool
     */
    abstract protected function matchUser(RoleModel $model);
}
