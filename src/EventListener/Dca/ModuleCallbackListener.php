<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use function array_unique;

/**
 * Class ModuleCallbackListener for the tl_module data container.
 */
final class ModuleCallbackListener
{
    /**
     * The workflow manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * ModuleCallbackListener constructor.
     *
     * @param WorkflowManager $workflowManager The workflow manager.
     */
    public function __construct(WorkflowManager $workflowManager)
    {
        $this->workflowManager = $workflowManager;
    }

    /**
     * Get all providers with a workflow.
     *
     * @return array
     */
    public function providerOptions(): array
    {
        $providers = [];

        foreach ($this->workflowManager->getWorkflows() as $workflow) {
            $providers[] = $workflow->getProviderName();
        }

        $providers = array_unique($providers);
        sort($providers);

        return $providers;
    }
}
