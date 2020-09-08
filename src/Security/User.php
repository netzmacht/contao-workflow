<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

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
     *
     * @return EntityId|null
     */
    public function getUserId(?UserInterface $user = null): ?EntityId;

    /**
     * Check if user as a given permission.
     *
     * @param Permission         $permission The permission to check.
     * @param UserInterface|null $user       The user to check. If empty the user the current security user is used.
     *
     * @return bool
     */
    public function hasPermission(Permission $permission, ?UserInterface $user = null): bool;

    /**
     * Get list of all permissions granted to the current user.
     *
     * @param UserInterface|null $user The user to check. If empty the user the current security user is used.
     *
     * @return Permission[]
     */
    public function getPermissions(?UserInterface $user = null): array;
}
