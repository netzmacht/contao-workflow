<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Permission;

use Contao\Model;

/**
 * @property string|int $id
 * @property string|int $tstamp
 * @property string     $source
 * @property string|int $source_id
 * @property string     $permission
 */
final class PermissionModel extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow_permission';
}
