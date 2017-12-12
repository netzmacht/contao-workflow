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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\History;

use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Interface HistoryRenderer.
 */
interface HistoryRenderer
{
    /**
     * Check if item is supported of the history renderer.
     *
     * @param Item     $item     Workflow item.
     * @param Workflow $workflow The workflow.
     *
     * @return bool
     */
    public function supports(Item $item, Workflow $workflow): bool;

    /**
     * Render the state history and return modified state history.
     *
     * @param Item     $item     Workflow item.
     * @param Workflow $workflow The workflow.
     * @param array    $data     The rendered state history.
     *
     * @return array
     */
    public function render(Item $item, Workflow $workflow, array $data): array;
}
