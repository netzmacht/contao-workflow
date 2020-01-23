<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Workflow;

use Contao\Model;

/**
 * WorkflowModel using Contao models.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Model
 *
 * @property int        $id           The workflow id.
 * @property array      $process      The process definition.
 * @property string     $type         The workflow type.
 * @property string     $providerName Provider name.
 * @property string     $label        Label.
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
