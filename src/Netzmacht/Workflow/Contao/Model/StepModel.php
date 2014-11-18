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
 * @property int $id
 * @property string $name
 * @property string $label
 * @property bool   $final
 * @property bool   $limitPermission
 * @property string $permission
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
     * @param $workflowId
     *
     * @return \Model\Collection|null
     */
    public static function findByWorkflow($workflowId)
    {
        return static::findBy(
            array('pid=?'),
            $workflowId,
            array('order' => 'name')
        );
    }
}
