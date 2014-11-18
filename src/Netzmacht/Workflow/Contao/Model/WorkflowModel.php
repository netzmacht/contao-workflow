<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Model;

/**
 * WorkflowModel using Contao models.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Model
 * @property int   $id      The workflow id.
 * @property array $process The process definition.
 */
class WorkflowModel extends \Model
{
    /**
     * Table name.
     *
     * @var string
     *
     */
    protected static $strTable = 'tl_workflow';

    public static function findByProviderAndType($providerName, $workflowType)
    {
        return static::findBy(
            array(static::$strTable . '.providerName=?', static::$strTable . '.type=?'),
            array($providerName, $workflowType)
        );
    }

    public static function findByProvider($providerName)
    {
        return static::findBy(
            'providerName', $providerName
        );
    }
}
