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


class Role
{
    /**
     * @var int
     */
    private $roleId;

    /**
     * @var string
     */
    private $name;

    /**
     * @param $roleId
     * @param $name
     */
    public function __construct($roleId, $name)
    {
        $this->roleId = $roleId;
        $this->name   = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function equals(Role $role)
    {
        return $this->getName() == $role->getName();
    }
}
