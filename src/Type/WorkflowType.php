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

declare(strict_types=1);

namespace Netzmacht\Contao\Workflow\Type;

use Netzmacht\Workflow\Flow\Workflow;

/**
 * Interface WorkflowType.
 *
 * @package Netzmacht\Contao\Workflow\Type
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
     * Configure the workflow after at build time.
     *
     * @param Workflow $workflow The workflow.
     *
     * @return void
     */
    public function configure(Workflow $workflow): void;

    /**
     * Get a list of supported provider names.
     *
     * @return array
     */
    public function getProviderNames(): array;

    /**
     * Get the item renderer.
     *
     * @return Renderer
     */
    public function getRenderer(): Renderer;
}
