<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Contao\Dca;


use Netzmacht\Contao\Workflow\Contao\Model\RoleModel;

class Transition
{
    public function getUserRoles()
    {
        $roles      = array();
        $collection = RoleModel::findAll(array('order' => 'name'));

        if ($collection) {
            while ($collection->next()) {
                $roles[$collection->id] = $collection->name;
            }
        }

        $roles = array(
            'system' => array('admin', 'guest'),
            'roles'  => $roles,
        );

        return $roles;
    }
}
