<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Type;

/**
 * Class DefaultWorkflowType.
 *
 * @package Netzmacht\Contao\Workflow\Type
 */
class DefaultWorkflowType implements WorkflowType
{
    /**
     * List of supported providers.
     *
     * @var string[]|array
     */
    private $supportedProviders;

    /**
     * DefaultWorkflowType constructor.
     *
     * @param array|string[] $supportedProviders
     */
    public function __construct(array $supportedProviders = [])
    {
        $this->supportedProviders = $supportedProviders;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'default';
    }

    /**
     * {@inheritDoc}
     */
    public function getProviderNames(): array
    {
        return $this->supportedProviders;
    }
}
