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

use Netzmacht\Workflow\Flow\Workflow;

/**
 * Interface WorkflowType.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Type
 */
interface WorkflowType
{
    /**
     * Get the name of the workflow.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Check if workflow type matches a type name.
     *
     * @param string $typeName The type name.
     *
     * @return bool
     */
    public function match(string $typeName): bool;

    /**
     * Configure the workflow after at build time.
     *
     * @param Workflow $workflow The workflow.
     * @param callable $next     Callable which triggers the next workflow configuration handler. Should be called by
     *                           bye implementation to make sure what the default behaviour is applied.
     *
     * @return void
     */
    public function configure(Workflow $workflow, callable $next): void;

    /**
     * Get a list of supported provider names.
     *
     * @return array
     */
    public function getProviderNames(): array;
}
