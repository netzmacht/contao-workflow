<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */


namespace Netzmacht\ContaoWorkflowBundle\Model\Step;

use Contao\Model;

/**
 * StepModel using Contao models.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Model
 *
 * @property int    $id              The model id.
 * @property string $name            The step name.
 * @property string $label           The step label.
 * @property bool   $final           Step is a final step.
 * @property bool   $limitPermission Limit the permission.
 * @property string $permission      The permission id.
 * @property string $triggerWorkflow The id of another workflow that should start when this step has been reached
 */
final class StepModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow_step';
}
