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

interface AclManager
{
    /**
     * @param Workflow $workflow
     *
     * @return Role[]
     */
    public function getRoles(Workflow $workflow);

    /**
     * @param Workflow $workflow
     * @param Role     $role
     *
     * @return bool
     */
    public function hasPermission(Workflow $workflow, Role $role);

    /**
     * @return bool
     */
    public function hasAdminPermissions();
}
