<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Action;

use Contao\Model;

/**
 * Class ActionModel provides access to tl_workflow_action table.
 *
 * @property int|string $id
 * @property int|string $pid
 * @property string     $type
 * @property string     $label
 * @property string     $ptable
 */
final class ActionModel extends Model
{
    /** @var string  */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $strTable = 'tl_workflow_action';
}
