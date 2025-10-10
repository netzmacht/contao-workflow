<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Security\Permission;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface User contains all granted permission for the current user.
 */
interface User
{
    /**
     * Get the entity id of the current user.
     *
     * @param UserInterface|null $user The user to check. If empty the user the current security user is used.
     */
    public function getUserId(UserInterface|null $user = null): EntityId|null;

    /**
     * Check if user as a given permission.
     *
     * @param Permission         $permission The permission to check.
     * @param UserInterface|null $user       The user to check. If empty the user the current security user is used.
     */
    public function hasPermission(Permission $permission, UserInterface|null $user = null): bool;

    /**
     * Get list of all permissions granted to the current user.
     *
     * @param UserInterface|null $user The user to check. If empty the user the current security user is used.
     *
     * @return Permission[]
     */
    public function getPermissions(UserInterface|null $user = null): array;
}
