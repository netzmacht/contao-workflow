<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Dca;

use Netzmacht\Workflow\Contao\Model\WorkflowModel;
use Netzmacht\Workflow\Security\Permission as WorkflowPermission;

/**
 * Class Permission initialize permission fields for the workflows for frontend and backend users.
 *
 * @package Netzmacht\Workflow\Contao\Dca\Table
 */
class Permission
{
    public function getAllPermissions()
    {
        $options    = array();
        $collection = WorkflowModel::findAll();


        if ($collection) {
            while ($collection->next()) {
                $permissions = deserialize($collection->permissions, true);

                foreach ($permissions as $permission) {
                    $workflow = $collection->label
                        ? ($collection->label . ' [' . $collection->name . ']')
                        : $collection->name;

                    $name = $collection->name . ':' . $permission['name'];
                    $options[$workflow][$name] = $permission['label'] ?: $permission['name'];
                }
            }
        }

        return $options;
    }

    public function getWorkflowPermissions($dataContainer)
    {
        if (!$dataContainer->activeRecord || !$dataContainer->activeRecord->pid) {
            return array();
        }

        $collection = WorkflowModel::findBy('id', $dataContainer->activeRecord->pid);
        $options    = array();

        if ($collection) {
            while ($collection->next()) {
                $permissions = deserialize($collection->permissions, true);

                foreach ($permissions as $config) {
                    $permission = WorkflowPermission::forWorkflowName(
                        $collection->name,
                        $config['name']
                    );

                    $options[(string) $permission] = $config['label'] ?: $config['name'];
                }
            }
        }

        return $options;
    }
}
