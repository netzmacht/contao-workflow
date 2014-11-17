<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao;

use Netzmacht\Workflow\Factory\Event\CreateUserEvent;
use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Security\Role;
use Netzmacht\Workflow\Security\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Boot implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateUserEvent::NAME => 'createUserRoles'
        );
    }

    /**
     * @param CreateUserEvent $event
     */
    public function createUserRoles(CreateUserEvent $event)
    {
        if (TL_MODE != 'BE' && TL_MODE == 'FE') {
            return;
        }

        $user  = $event->getUser();
        $roles = array();

        if (TL_MODE == 'BE') {
            $roleName   = 'be_user';
            $contaoUser = \BackendUser::getInstance();
        } else {
            $roleName   = 'fe_member';
            $contaoUser = \FrontendUser::getInstance();
        }

        foreach ((array) $contaoUser->workflowPermissions as $permissionName) {
            $permission = Permission::fromString($permissionName);
            $role       = $this->createRole($roles, $roleName, $permission, $contaoUser, $user);

            $role->addPermission($permission);
        }
    }

    /**
     * @param Role[]      $roles
     * @param string     $roleName
     * @param Permission $permission
     * @param \User      $contaoUser
     * @param User       $user
     *
     * @return Role
     */
    private function createRole(&$roles, $roleName, Permission $permission, $contaoUser, User $user)
    {
        $workflow = $permission->getWorkflowName();

        if (!isset($roles[$workflow])) {
            $role = new Role(
                $roleName,
                $permission->getWorkflowName(),
                null,
                array('user' => $contaoUser)
            );

            $roles[$workflow] = $role;
            $user->assign($role);
        }

        return $roles[$workflow];
    }
}
