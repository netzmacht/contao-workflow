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

namespace Netzmacht\Contao\Workflow\Backend\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class GetProviderNamesEvent is emitted when all supported provider names of a workflow type are collected.
 *
 * @package Netzmacht\Contao\Workflow\Backend\Event
 */
class GetProviderNamesEvent extends Event
{
    const NAME = 'workflow.backend.get-provider-names';

    /**
     * Workflow type.
     *
     * @var string
     */
    private $type;

    /**
     * Provider names.
     *
     * @var array
     */
    private $providerNames = array();

    /**
     * Construct.
     *
     * @param string $type The workflow type.
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Get the type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get all provider names.
     *
     * @return array
     */
    public function getProviderNames()
    {
        return $this->providerNames;
    }

    /**
     * Set the provider names.
     *
     * @param array $providerNames The provider names.
     *
     * @return $this
     */
    public function setProviderNames($providerNames)
    {
        $this->providerNames = $providerNames;

        return $this;
    }
}
