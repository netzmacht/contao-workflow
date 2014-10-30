<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Contao\Model;

/**
 * WorkflowModel using Contao models.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Model
 */
class WorkflowModel extends \Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow';

    public static function findByType($workflowType)
    {
        return static::findBy(
            'type', $workflowType
        );
    }

    public static function findByTypeAndProvider($workflowType, $providerName)
    {
        return static::findBy(
            array(static::$strTable . '.type=?', static::$strTable . '.providerName=?'),
            array($workflowType, $providerName)
        );
    }
}
