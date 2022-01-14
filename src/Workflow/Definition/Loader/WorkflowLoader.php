<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader;

use Netzmacht\Workflow\Flow\Workflow;

interface WorkflowLoader
{
    /**
     * Load the workflows optional limited for a provider.
     *
     * @return Workflow[]|array
     */
    public function load(): array;
}
