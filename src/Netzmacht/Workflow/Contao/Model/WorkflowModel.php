<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Workflow\Contao\Model;

/**
 * WorkflowModel using Contao models.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Model
 *
 * @property int   $id      The workflow id.
 * @property array $process The process definition.
 */
class WorkflowModel extends \Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow';

    /**
     * Find workflows by the provider name and type.
     *
     * @param string $providerName The provider name.
     * @param string $workflowType The workflow type.
     *
     * @return \Model\Collection|null
     */
    public static function findByProviderAndType($providerName, $workflowType)
    {
        return static::findBy(
            array(static::$strTable . '.providerName=?', static::$strTable . '.type=?'),
            array($providerName, $workflowType)
        );
    }

    /**
     * Find workflow definitions by the provider name.
     *
     * @param string $providerName The provider name.
     *
     * @return \Model\Collection|null
     */
    public static function findByProvider($providerName)
    {
        return static::findBy('providerName', $providerName);
    }
}
