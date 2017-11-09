<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Model;

/**
 * Class PermissionModel.
 *
 * @property int    tstamp
 * @property string source
 * @property int    source_id
 * @property  permission
 * @package Netzmacht\Contao\Workflow\Model
 */
class PermissionModel extends \Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow_permission';
}
