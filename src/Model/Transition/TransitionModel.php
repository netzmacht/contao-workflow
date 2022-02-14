<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Transition;

use Contao\Model;
use Contao\Model\Collection;

/**
 * TransitionModel using Contao models.
 *
 * @property string|int  $id
 * @property string|int  $pid
 * @property string      $name
 * @property string      $label
 * @property string|bool $final
 * @property string|bool $limitPermission
 * @property string      $permission
 * @property string|int  $stepTo
 * @property string      $type
 * @property string      $workflow
 */
final class TransitionModel extends Model
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
     * @return TransitionModel[]|Collection|null
     *
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public static function findByWorkflow(int $workflowId)
    {
        return self::findBy(
            [self::$strTable . '.active=1', self::$strTable . '.pid=?'],
            $workflowId,
            ['order' => self::$strTable . '.sorting']
        );
    }
}
