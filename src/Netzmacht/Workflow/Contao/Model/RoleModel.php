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
 * RoleModel using Contao models.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Model
 */
class RoleModel extends \Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow_role';

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
