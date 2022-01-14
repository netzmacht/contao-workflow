<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Step;

use Contao\Model;

/**
 * StepModel using Contao models.
 *
 * @property int|string      $id              The model id.
 * @property string          $name            The step name.
 * @property string          $label           The step label.
 * @property bool|string|int $final           Step is a final step.
 * @property bool|string|int $limitPermission Limit the permission.
 * @property string          $permission      The permission id.
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
