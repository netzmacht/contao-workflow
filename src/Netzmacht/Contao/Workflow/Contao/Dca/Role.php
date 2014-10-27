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


class Role
{
    public function getUsers()
    {
        $options = array();
        $users   = \UserModel::findAll(array('order' => 'name'));

        if ($users) {
            while ($users->next()) {
                $options[$users->id] = sprintf('%s [%s]', $users->name, $users->username);
            }
        }

        return $options;
    }

    public function getUserGroups()
    {
        $options = array();
        $groups  = \UserGroupModel::findAll(array('order' => 'name'));

        if ($groups) {
            while ($groups->next()) {
                $options[$groups->id] = $groups->name;
            }
        }

        return $options;
    }

    public function getMembers()
    {
        $options = array();
        $members = \MemberModel::findAll(array('order' => 'firstname, lastname'));

        if ($members) {
            while ($members->next()) {
                $options[$members->id] = sprintf('%s %s', $members->firstname, $members->lastname);

                if ($members->username) {
                    $options[$members->id] .= sprintf(' [%s]', $members->username);
                }
            }
        }

        return $options;
    }

    public function getMemberGroups()
    {
        $options = array();
        $groups  = \MemberGroupModel::findAll(array('order' => 'name'));

        if ($groups) {
            while ($groups->next()) {
                $options[$groups->id] = $groups->name;
            }
        }

        return $options;
    }
}
