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
        $contaoUser = $this->getContaoUser($container);
        $user       = $this->createUser($container['event-dispatcher'], $contaoUser);

        $container['workflow.security.user'] = function () use ($user) {
            return $user;
        };
    }

    /**
     * Listener for the CreateUserEvent.
     *
     * Load user permissions for current frontend or backend user.
     *
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     * @param \User           $contaoUser      The contao user.
     *
     * @return User
     */
    public function createUser(EventDispatcher $eventDispatcher, \User $contaoUser)
    {
        $this->initializePermissionTranslations();
        $user = new User();

        if (TL_MODE == 'BE') {
            $this->createBackendUserRole($user, $contaoUser);
        } elseif (TL_MODE == 'FE') {
            $this->createFrontendMemberRole($user, $contaoUser);
        }

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
     * Get the Contao user from the container.
     *
     * @param \Pimple $container The dependency container.
     *
     * @return null
     */
    private function getContaoUser(\Pimple $container)
    {
        // Fetch exception for unknown TL_MODE. Workflow can workflow without that, so just get null back.
        try {
            /** @var \User $user */
            $user = $container['user'];
        } catch (\Exception $e) {
            return null;
        }

        if ($container['workflow.security.authenticate']) {
            $user->authenticate();
        }

        return $user;
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
