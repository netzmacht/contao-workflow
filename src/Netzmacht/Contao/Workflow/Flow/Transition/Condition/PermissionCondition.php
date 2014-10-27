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

class PermissionCondition implements Condition
{
    /**
     * @var AclManager
     */
    private $aclManager;

    /**
     * @var bool
     */
    private $ignoreAdmin = false;

    /**
     * @var Role[]
     */
    private $transitionRoles = array();

    /**
     * @param AclManager $aclManager
     * @param bool       $ignoreAdmin
     *
     * @internal param \BackendUser $user
     */
    public function __construct(AclManager $aclManager, $ignoreAdmin = false)
    {
        $this->aclManager  = $aclManager;
        $this->ignoreAdmin = $ignoreAdmin;
    }

    /**
     * @return boolean
     */
    public function isAdminPermissionIgnored()
    {
        return $this->ignoreAdmin;
    }

    /**
     * @param boolean $ignoreAdmin
     *
     * @return $this
     */
    public function ignoreAdminPermission($ignoreAdmin)
    {
        $this->ignoreAdmin = $ignoreAdmin;

        return $this;
    }

    /**
     * @param Transition $transition
     * @param Entity     $entity
     * @param Context    $context
     *
     * @return bool
     */
    public function match(Transition $transition, Entity $entity, Context $context)
    {
        if (!$this->ignoreAdmin && $this->aclManager->hasAdminPermissions()) {
            return true;
        }

        foreach ($this->transitionRoles as $role) {
            if ($this->aclManager->hasPermission($role)) {
                return true;
            }
        }

        return false;
    }
}
