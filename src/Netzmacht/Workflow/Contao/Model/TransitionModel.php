<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Workflow\Contao\Model;

/**
 * TransitionModel using Contao models.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Model
 * @property int $id
 * @property string $name
 * @property string $label
 * @property bool   $final
 * @property bool   $limitPermission
 * @property string $permission
 * @property int    $stepTo
 */
class TransitionModel extends \Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow_transition';

    /**
     * Find transition by workflow id.
     *
     * @param int $workflowId The workflow id.
     *
     * @return \Model\Collection|null
     */
    public static function findByWorkflow($workflowId)
    {
        return static::findBy(
            array(static::$strTable . '.active=1', static::$strTable . '.pid=?'),
            $workflowId,
            array('order' => static::$strTable . '.sorting')
        );
    }
}
