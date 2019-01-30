<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Type;

/**
 * Class DefaultWorkflowType.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Type
 */
final class DefaultWorkflowType extends AbstractWorkflowType
{
    /**
     * DefaultWorkflowType constructor.
     *
     * @param array|string[] $supportedProviders Supported providers.
     */
    public function __construct(array $supportedProviders = [])
    {
        parent::__construct('default_type', $supportedProviders);
    }
}
