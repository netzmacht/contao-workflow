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

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Type;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Workflow\TypeCondition;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class AbstractWorkflowType provides default implementation for common WorkflowType methods.
 */
abstract class AbstractWorkflowType implements WorkflowType
{
    /**
     * Workflow type name.
     *
     * @var string
     */
    private $name;

    /**
     * List of supported providers.
     *
     * @var string[]|array
     */
    private $supportedProviders;

    /**
     * AbstractWorkflowType constructor.
     *
     * @param string         $name               The workflow type name.
     * @param array|string[] $supportedProviders Supported providers.
     */
    public function __construct(string $name, array $supportedProviders)
    {
        $this->name               = $name;
        $this->supportedProviders = $supportedProviders;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function match(string $typeName): bool
    {
        return $this->getName() === $typeName;
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Workflow $workflow, callable $next): void
    {
        $workflow->addCondition(new TypeCondition($this));
        $next();
    }

    /**
     * {@inheritDoc}
     */
    public function getProviderNames(): array
    {
        return $this->supportedProviders;
    }
}
