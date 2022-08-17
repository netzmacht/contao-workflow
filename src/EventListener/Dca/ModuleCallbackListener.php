<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Netzmacht\Workflow\Manager\Manager as WorkflowManager;

use function array_unique;
use function sort;

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
     * @param WorkflowManager $workflowManager The workflow manager.
     */
    public function __construct(WorkflowManager $workflowManager)
    {
        $this->workflowManager = $workflowManager;
    }

    /**
     * Get all providers with a workflow.
     *
     * @return list<string>
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
