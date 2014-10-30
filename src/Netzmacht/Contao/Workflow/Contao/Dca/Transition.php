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
use Netzmacht\Contao\Workflow\Contao\Model\StepModel;

/**
 * Class Transition used for tl_workflow_transition callbacks.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class Transition
{
    /**
     * Get user roles.
     *
     * @return array
     */
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

    public function getStepsTo()
    {
        $steps      = array();
        $collection = StepModel::findAll(array('order' => 'name'));

        if ($collection) {
            while ($collection->next()) {
                $steps[$collection->id] = $collection->label;

                if ($collection->final) {
                    $steps[$collection->id] .= ' [final]';
                }
            }
        }

        return $steps;
    }
}
