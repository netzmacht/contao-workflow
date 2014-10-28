<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Transition\Condition;

use Netzmacht\Contao\Workflow\Acl\AclManager;
use Netzmacht\Contao\Workflow\Acl\Role;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Transition\Condition;

/**
 * Class PermissionCondition handles permission limiting for transitions.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Transition\Condition
 */
class PermissionCondition implements Condition
{
    /**
     * The ACL Manager.
     *
     * @var AclManager
     */
    private $aclManager;

    /**
     * Ignore admin permissions.
     *
     * @var bool
     */
    private $ignoreAdmin = false;

    /**
     * Construct.
     *
     * @param AclManager $aclManager  The ACL manager.
     * @param bool       $ignoreAdmin Ignore admin permissions.
     */
    public function __construct(AclManager $aclManager, $ignoreAdmin = false)
    {
        $this->aclManager  = $aclManager;
        $this->ignoreAdmin = $ignoreAdmin;
    }

    /**
     * Consider if admin permissions should be ignored.
     *
     * @return bool
     */
    public function isAdminPermissionIgnored()
    {
        return $this->ignoreAdmin;
    }

    /**
     * Ignore admin permissions.
     *
     * @param bool $ignoreAdmin Ignore admin permissions.
     *
     * @return $this
     */
    public function ignoreAdminPermission($ignoreAdmin)
    {
        $this->ignoreAdmin = $ignoreAdmin;

        return $this;
    }

    /**
     * Consider if permision condition matches.
     *
     * @param Transition $transition The transition being in.
     * @param Entity     $entity     The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return bool
     */
    public function match(Transition $transition, Entity $entity, Context $context)
    {
        if (!$this->ignoreAdmin && $this->aclManager->hasAdminPermissions()) {
            return true;
        }

        $workflow = $transition->getWorkflow();

        foreach ($transition->getRoles() as $role) {
            if ($this->aclManager->hasPermission($workflow, $role)) {
                return true;
            }
        }

        return false;
    }
}
