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

use Netzmacht\Contao\Workflow\Flow\Workflow;

/**
 * Interface AclManager handles all roles for a workflow.
 *
 * @package Netzmacht\Contao\Workflow\Acl
 */
interface AclManager
{
    /**
     * Get roles for a workflow.
     *
     * @param Workflow $workflow Current workflow.
     *
     * @return Role[]
     */
    public function getRoles(Workflow $workflow);

    /**
     * Consider if current user have enough permission of given role.
     *
     * @param Workflow $workflow Current workflow.
     * @param Role     $role     Role which should be granted.
     *
     * @return bool
     */
    public function hasPermission(Workflow $workflow, Role $role);

    /**
     * Check if user has admin permission.
     *
     * @return bool
     */
    public function hasAdminPermissions();
}
