<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Model;

/**
 * StepModel using Contao models.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Model
 *
 * @property int    $id              The model id.
 * @property string $name            The step name.
 * @property string $label           The step label.
 * @property bool   $final           Step is a final step.
 * @property bool   $limitPermission Limit the permission.
 * @property string $permission      The permission id.
 */
class StepModel extends \Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow_step';

    /**
     * Find by workflow id.
     *
     * @param int $workflowId The workflow id.
     *
     * @return \Model\Collection|null
     */
    public static function findByWorkflow($workflowId)
    {
        return static::findBy(array('pid=?'), $workflowId, array('order' => 'name'));
    }
}
