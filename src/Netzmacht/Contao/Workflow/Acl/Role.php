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

/**
 * Class Role describes an user role.
 *
 * @package Netzmacht\Contao\Workflow\Acl
 */
class Role
{
    /**
     * The role id.
     *
     * @var int
     */
    private $roleId;

    /**
     * The role name.
     *
     * @var string
     */
    private $name;

    /**
     * Construct.
     *
     * @param int    $roleId Role id.
     * @param string $name   Role name.
     */
    public function __construct($roleId, $name)
    {
        $this->roleId = $roleId;
        $this->name   = $name;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the id.
     *
     * @return int
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Consider if role equals to another role.
     *
     * @param Role $role Role to compare with.
     *
     * @return bool
     */
    public function equals(Role $role)
    {
        return $this->getName() == $role->getName();
    }
}
