<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View;

interface Renderer
{
    /**
     * Check if renderer supports the workflow item view.
     *
     * @param View $view The workflow item view.
     */
    public function supports(View $view): bool;

    /**
     * Render the view.
     *
     * @param View $view The workflow item view.
     */
    public function render(View $view): void;
}
