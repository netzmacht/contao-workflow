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
 * Class DelegatingHistoryRenderer.
 */
class DelegatingHistoryRenderer implements HistoryRenderer
{
    /**
     * List of workflow renderer.
     *
     * @var HistoryRenderer[]|iterable
     */
    private $renderer;

    /**
     * DelegatingHistoryRenderer constructor.
     *
     * @param iterable|HistoryRenderer[] $renderer List of history renderer.
     */
    public function __construct(iterable $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Item $item, Workflow $workflow): bool
    {
        foreach ($this->renderer as $renderer) {
            if ($renderer->supports($item, $workflow)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function render(Item $item, Workflow $workflow, array $data): array
    {
        foreach ($this->renderer as $renderer) {
            if ($renderer->supports($item, $workflow)) {
                $data = $renderer->render($item, $workflow, $data);
            }
        }

        return $data;
    }
}
