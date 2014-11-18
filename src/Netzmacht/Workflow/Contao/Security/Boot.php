<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Security;

use Netzmacht\Workflow\Contao\Model\WorkflowModel;
use Netzmacht\Workflow\Factory\Event\CreateUserEvent;
use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Security\Role;
use Netzmacht\Workflow\Security\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Boot boots the security context.
 *
 * @package Netzmacht\Workflow\Contao\Security
 */
class Boot implements EventSubscriberInterface
{
    function __construct()
    {
        \Controller::loadLanguageFile('workflow');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateUserEvent::NAME => 'createUserRoles',
        );
    }

    /**
     * Listener for the CreateUserEvent.
     *
     * Load user permissions for current frontend or backend user.
     *
     * @param CreateUserEvent $event
     */
    public function createUserRoles(CreateUserEvent $event)
    {
        $this->initializePermissionTranslations();
        $user = $event->getUser();

        if (TL_MODE == 'BE') {
            $this->createBackendUserRole($user);
        } elseif (TL_MODE == 'FE') {
            $this->createFrontendMemberRole($user);
        }
    }

    /**
     * Create permission for the backend user.
     * @param User $user
     */
    private function createBackendUserRole(User $user)
    {
        $roles      = array();
        $roleName   = 'be_user';
        $contaoUser = \BackendUser::getInstance();

        foreach ((array) $contaoUser->workflow as $permissionName) {
            $permission = Permission::fromString($permissionName);
            $this->addPermissionToRole($roles, $roleName, $permission, $contaoUser, $user);
        }

        if ($contaoUser->isAdmin) {
            $workflows = WorkflowModel::findAll();

            if ($workflows->next()) {
                $permission = Permission::forWorkflowNameAndPermissionId($workflows->name, 'contao-admin');
                $this->addPermissionToRole($roles, $roleName, $permission, $contaoUser, $user);
            }
        }
    }

    /**
     * @param User $user
     */
    private function createFrontendMemberRole(User $user)
    {
        $roleName    = 'fe_member';
        $roles       = array();
        $contaoUser  = \FrontendUser::getInstance();
        $permissions = $this->getMemberPermissions($contaoUser);

        foreach ($permissions as $permissionName) {
            $permission = Permission::fromString($permissionName);
            $this->addPermissionToRole($roles, $roleName, $permission, $contaoUser, $user);
        }

        if (FE_USER_LOGGED_IN !== true) {
            $workflows = WorkflowModel::findAll();

            if ($workflows->next()) {
                $permission = Permission::forWorkflowNameAndPermissionId($workflows->name, 'contao-guest');
                $this->addPermissionToRole($roles, $roleName, $permission, $contaoUser, $user);
            }
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
    private function addPermissionToRole(&$roles, $roleName, Permission $permission, $contaoUser, User $user)
    {
        $workflow = $permission->getWorkflowName();

        if (!isset($roles[$workflow])) {
            $label = isset($GLOBALS['TL_LANG']['workflow']['roles'][$roleName])
                ? $GLOBALS['TL_LANG']['workflow']['roles'][$roleName]
                : null;

            $role = new Role(
                $roleName,
                $permission->getWorkflowName(),
                $label,
                array('user' => $contaoUser)
            );

            $roles[$workflow] = $role;
            $user->assign($role);
        }

        $roles[$workflow]->addPermission($permission);

        return $roles[$workflow];
    }

    /**
     *
     * @return array
     */
    private function getMemberPermissions($contaoUser)
    {
        $groups      = \MemberGroupModel::findMultipleByIds($contaoUser->groups);
        $permissions = array();

        if ($groups) {
            while ($groups->next()) {
                $permissions = array_merge($permissions, deserialize($groups->workflow, true));
            }

            $permissions = array_unique($permissions);
        }

        return $permissions;
    }

    /**
     * Initialize role translations
     */
    private function initializePermissionTranslations()
    {
        $workflows = WorkflowModel::findAll();

        if (!$workflows) {
            return;
        }

        while ($workflows->next()) {
            $permissions = deserialize($workflows->permissions, true);

            foreach ($permissions as $permission) {
                $name = $workflows->name . ':' . $permission['name'];

                // only set if not already set. allow to customize permission labels by language files
                if (!isset($GLOBALS['TL_LANG']['workflow_permissions'][$name])) {
                    $GLOBALS['TL_LANG']['workflow_permissions'][$name] = $permission['label'] ?: $permission['name'];
                }
            }
        }
    }
}
