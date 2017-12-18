<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View;

use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Interface Renderer
 */
interface Renderer
{
    /**
     * Check if renderer supports current workflow in its context.
     *
     * @param Workflow              $workflow The workflow.
     * @param Step|Transition|State $context  The current context.
     *
     * @return bool
     */
    public function supports(Workflow $workflow, $context): bool;

    /**
     * Render the view.
     *
     * @param View $view The workflow item view.
     *
     * @return void
     */
    public function render(View $view): void;
}
