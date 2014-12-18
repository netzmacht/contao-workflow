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

namespace Netzmacht\Workflow\Contao;

use Netzmacht\Workflow\Contao\Definition\Event\BuildUserEvent;
use Netzmacht\Workflow\Contao\Model\WorkflowModel;
use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Security\Role;
use Netzmacht\Workflow\Security\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class Boot boots the workflow security context.
 *
 * @package Netzmacht\Workflow\Contao\Security
 */
class Boot
{
    /**
     * Construct.
     */
    public function __construct()
    {
        \Controller::loadLanguageFile('workflow');
        \Controller::loadLanguageFile('workflow_permissions');
    }

    /**
     * Initialize the user.
     *
     * @param \Pimple $container The dependency container.
     *
     * @return void
     */
    public function startup(\Pimple $container)
    {
        $container['workflow.security.user'] = $container->share(
            function () use ($container) {
                $user = $this->createUser($container['event-dispatcher']);

                try {
                    $contaoUser = $container['user'];
                    $this->initializeContaoUser($user, $contaoUser);
                } catch (\Exception $e) {
                    // Ignore error. Fetching user from container could throw an error for a unknown TL_MODE.
                }

                return $user;
            }
        );
    }

    /**
     * Initialize Contao user using postLogin hook.
     *
     * @param User  $user       Security user.
     * @param \user $contaoUser Contao user.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function initializeContaoUser(User $user, \user $contaoUser)
    {
        if ($contaoUser instanceof \BackendUser) {
            $this->createBackendUserRole($user, $contaoUser);
        } elseif ($contaoUser instanceof \FrontendUser) {
            $this->createFrontendMemberRole($user, $contaoUser);
        }
    }

    /**
     * Listener for the CreateUserEvent.
     *
     * Load user permissions for current frontend or backend user.
     *
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     *
     * @return User
     */
    public function createUser(EventDispatcher $eventDispatcher)
    {
        $this->initializePermissionTranslations();

        $user  = new User();
        $event = new BuildUserEvent($user);

        $eventDispatcher->dispatch($event::NAME, $event);

        return $user;
    }

    /**
     * Create permission for the backend user.
     *
     * @param User  $user       The security user.
     * @param \User $contaoUser The contao user.
     *
     * @return void
     */
    private function createBackendUserRole(User $user, \User $contaoUser)
    {
        $roles    = array();
        $roleName = 'be_user';

        foreach ((array) $contaoUser->workflow as $permissionName) {
            $permission = Permission::fromString($permissionName);
            $this->addPermissionToRole($roles, $roleName, $permission, $contaoUser, $user);
        }

        if ($contaoUser->isAdmin) {
            $workflows = WorkflowModel::findAll();

            if ($workflows->next()) {
                $permission = Permission::forWorkflowName($workflows->name, 'contao-admin');
                $this->addPermissionToRole($roles, $roleName, $permission, $contaoUser, $user);
            }
        }
    }

    /**
     * Create frontend member role.
     *
     * @param User  $user       The security user.
     * @param \User $contaoUser The contao user.
     *
     * @return void
     */
    private function createFrontendMemberRole(User $user, \User $contaoUser)
    {
        $roleName    = 'fe_member';
        $roles       = array();
        $permissions = $this->getMemberPermissions($contaoUser);

        foreach ($permissions as $permissionName) {
            $permission = Permission::fromString($permissionName);
            $this->addPermissionToRole($roles, $roleName, $permission, $contaoUser, $user);
        }

        if (FE_USER_LOGGED_IN !== true) {
            $workflows = WorkflowModel::findAll();

            if ($workflows->next()) {
                $permission = Permission::forWorkflowName($workflows->name, 'contao-guest');
                $this->addPermissionToRole($roles, $roleName, $permission, $contaoUser, $user);
            }
        }
    }

    /**
     * Add permission to the role.
     *
     * @param Role[]     $roles      Already created roles.
     * @param string     $roleName   The role name.
     * @param Permission $permission The permission name.
     * @param \User      $contaoUser The Contao user.
     * @param User       $user       The security user.
     *
     * @return Role
     */
    private function addPermissionToRole(&$roles, $roleName, Permission $permission, $contaoUser, User $user)
    {
        $workflow = $permission->getWorkflowName();

        if (!isset($roles[$workflow])) {
            $role = new Role(
                $roleName,
                $permission->getWorkflowName(),
                $this->translateLabel($roleName),
                array('user' => $contaoUser)
            );

            $roles[$workflow] = $role;
            $user->assign($role);
        }

        $roles[$workflow]->addPermission($permission);

        return $roles[$workflow];
    }

    /**
     * Get the member permissions.
     *
     * @param \User $contaoUser The contao user.
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
     * Initialize role translations.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
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

            $GLOBALS['TL_LANG']['workflow_permissions'][$workflows->name . ':be_admin'] =
                $GLOBALS['TL_LANG']['workflow_permissions']['be_admin'];

            $GLOBALS['TL_LANG']['workflow_permissions'][$workflows->name . ':fe_guest'] =
                $GLOBALS['TL_LANG']['workflow_permissions']['fe_guest'];
        }
    }

    /**
     * Get translated role name.
     *
     * @param string $roleName The role name.
     *
     * @return string|null
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function translateLabel($roleName)
    {
        return isset($GLOBALS['TL_LANG']['workflow']['roles'][$roleName])
            ? $GLOBALS['TL_LANG']['workflow']['roles'][$roleName]
            : null;
    }
}
