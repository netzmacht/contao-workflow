<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;

use function count;

final class DelegatingRenderer implements Renderer
{
    /**
     * View renderer.
     *
     * @var Renderer[]
     */
    private $renderer = [];

    /**
     * @param iterable|Renderer[] $renderer View renderer.
     */
    public function __construct(iterable $renderer)
    {
        foreach ($renderer as $rendererInstance) {
            $this->renderer[] = $rendererInstance;
        }
    }

    public function supports(View $view): bool
    {
        return count($this->renderer) > 0;
    }

    public function render(View $view): void
    {
        foreach ($this->renderer as $renderer) {
            if (! $renderer->supports($view)) {
                continue;
            }

            $renderer->render($view);
        }
    }
}
