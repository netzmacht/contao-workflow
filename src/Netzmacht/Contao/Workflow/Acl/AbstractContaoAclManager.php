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
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Acl\Role;

/**
 * Class AbstractContaoAclManager implements the acl manager as base class for frontend/backend users.
 *
 * @package Netzmacht\Contao\Workflow\Acl
 */
abstract class AbstractContaoAclManager implements AclManager
{
    /**
     * The user object.
     *
     * @var \User
     */
    protected $user;

    /**
     * Construct.
     *
     * @param \User $user The user object.
     */
    public function __construct(\User $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * Consider if user has the role.
     *
     * @param RoleModel $model Given role model.
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
     * Match role against user groups.
     *
     * @param RoleModel $model Current role.
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
     * Match against current user.
     *
     * @param RoleModel $model Current role model.
     *
     * @return bool
     */
    abstract protected function matchUser(RoleModel $model);
}
