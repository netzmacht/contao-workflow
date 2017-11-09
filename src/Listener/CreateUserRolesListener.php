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

namespace Netzmacht\Contao\Workflow\Listener;

use Contao\BackendUser;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface as ContaoFramework;
use Contao\FrontendUser;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Workflow\Model\WorkflowModel;
use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Security\Role;
use Netzmacht\Workflow\Security\User;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class CreateUserRolesListener.
 *
 * @package Netzmacht\Contao\Workflow\Listener
 */
final class CreateUserRolesListener
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Security user.
     *
     * @var User
     */
    private $user;

    /**
     * CreateUserRolesListener constructor.
     *
     * @param ContaoFramework     $framework
     * @param RequestScopeMatcher $scopeMatcher
     * @param RepositoryManager   $repositoryManager
     * @param User                $user
     * @param Translator          $translator
     */
    public function __construct(
        ContaoFramework $framework,
        RequestScopeMatcher $scopeMatcher,
        RepositoryManager $repositoryManager,
        User $user,
        Translator $translator
    ) {
        $this->framework         = $framework;
        $this->scopeMatcher      = $scopeMatcher;
        $this->repositoryManager = $repositoryManager;
        $this->translator        = $translator;
        $this->user              = $user;
    }

    /**
     * Initialize the user roles on system initialization.
     */
    public function onInitializeSystem()
    {
        $user = new User();

        if ($this->scopeMatcher->isFrontendRequest()) {
            /** @var FrontendUser $contaoUser */
            $contaoUser = $this->framework->createInstance(FrontendUser::class);
            $this->createFrontendMemberRole($user, $contaoUser);
        } elseif ($this->scopeMatcher->isBackendRequest()) {
            /** @var BackendUser $backendUser */
            $backendUser = $this->framework->createInstance(BackendUser::class);
            $this->createBackendUserRole($user, $backendUser);
        }
    }

    /**
     * Create permission for the backend user.
     *
     * @param User        $user        The security user.
     * @param BackendUser $backendUser The contao user.
     *
     * @return void
     */
    private function createBackendUserRole(User $user, BackendUser $backendUser)
    {
        $roles    = array();
        $roleName = 'be_user';

        foreach ((array) $backendUser->workflow as $permissionName) {
            $permission = Permission::fromString($permissionName);
            $this->addPermissionToRole($roles, $roleName, $permission, $backendUser, $user);
        }

        if ($backendUser->isAdmin) {
            $workflows = $this->repositoryManager->getRepository(WorkflowModel::class)->findAll();

            while ($workflows->next()) {
                $permission = Permission::forWorkflowName($workflows->name, 'contao-admin');
                $this->addPermissionToRole($roles, $roleName, $permission, $backendUser, $user);
            }
        }
    }

    /**
     * Create frontend member role.
     *
     * @param User         $user         The security user.
     * @param FrontendUser $frontendUser The contao user.
     *
     * @return void
     */
    private function createFrontendMemberRole(User $user, FrontendUser $frontendUser)
    {
        $roleName    = 'fe_member';
        $roles       = array();
        $permissions = $this->getMemberPermissions($frontendUser);

        foreach ($permissions as $permissionName) {
            $permission = Permission::fromString($permissionName);
            $this->addPermissionToRole($roles, $roleName, $permission, $frontendUser, $user);
        }

        if (FE_USER_LOGGED_IN !== true) {
            $workflows = $this->repositoryManager->getRepository(WorkflowModel::class)->findAll();

            while ($workflows->next()) {
                $permission = Permission::forWorkflowName($workflows->name, 'contao-guest');
                $this->addPermissionToRole($roles, $roleName, $permission, $frontendUser, $user);
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
                $this->translator->trans('role.' . $roleName, [], 'contao_workflow') ?: $roleName,
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
}
