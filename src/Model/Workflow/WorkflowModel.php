<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Workflow;

use Contao\Model;

/**
 * WorkflowModel using Contao models.
 *
 * @property string|int        $id           The workflow id.
 * @property string|array      $process      The process definition.
 * @property string            $type         The workflow type.
 * @property string            $providerName Provider name.
 * @property string            $label        Label.
 * @property array|string|null $permissions  Defined permissions.
 */
final class WorkflowModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow';
}
