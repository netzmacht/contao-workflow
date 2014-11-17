<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Dca\Table;

use Netzmacht\Workflow\Contao\Model\WorkflowModel;

/**
 * Class Permission initialize permission fields for the workflows for frontend and backend users.
 *
 * @package Netzmacht\Workflow\Contao\Dca\Table
 */
class Permission
{
    protected $providerNames = array('tl_user', 'tl_member', 'tl_user_group', 'tl_member_group');

    /**
     * @param string $providerName Name of the data container provider.
     */
    public function createPermissionFields($providerName)
    {
        if (! in_array($providerName, $this->providerNames)) {
            return;
        }

        $workflows = WorkflowModel::findAll();

        if (!$workflows) {
            return;
        }

        $palettes = array();
        while ($workflows->next()) {
            $palettes[] = $this->createPermissionDca($workflows->current(), $providerName);
        }

        $this->addToPalettes($palettes, $providerName);
    }

    /**
     * @param WorkflowModel $workflowModel
     * @param string        $providerName
     *
     * @return string
     */
    private function createPermissionDca(WorkflowModel $workflowModel, $providerName)
    {
        $GLOBALS['TL_DCA'][$providerName]['fields']['workflow_' . $workflowModel->name] = array(
            'label'     => array(
                $workflowModel->label,
                sprintf($GLOBALS['TL_LANG'][$providerName]['workflow_permission_label'], $workflowModel->label)
            ),
            'inputType' => 'checkbox',
            'options'   => $this->createPermissionOptions($workflowModel->roles),
            'eval'      => array(
                'tl_class' => 'clr',
                'multiple' => true,
            ),
            'sql' => 'mediumblob NULL'
        );

        return 'workflow_' . $workflowModel->name;
    }

    /**
     * Create role options.
     *
     * @param array $roles Role data.
     *
     * @return array
     */
    private function createPermissionOptions($roles)
    {
        $options = array();

        foreach ($roles as $role) {
            $name           = standardize($role);
            $options[$name] = $role;
        }

        sort($options);

        return $options;
    }

    /**
     * @param $palettes
     * @param $providerName
     */
    private function addToPalettes($palettes, $providerName)
    {
        foreach (array_keys($GLOBALS['TL_DCA'][$providerName]['palettes']) as $name) {
            if ($name === '__selector__') {
                continue;
            }

            $GLOBALS['TL_DCA'][$providerName]['palettes'][$name] .= '{workflow_data}';
            $GLOBALS['TL_DCA'][$providerName]['palettes'][$name] .= implode(',', $palettes);
            $GLOBALS['TL_DCA'][$providerName]['palettes'][$name] .= ';';
        }
    }
}
