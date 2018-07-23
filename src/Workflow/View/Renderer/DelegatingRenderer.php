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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;

/**
 * Class DelegatingRenderer
 */
final class DelegatingRenderer implements Renderer
{
    /**
     * View renderer.
     *
     * @var Renderer[]|iterable
     */
    private $renderer;

    /**
     * DelegatingRenderer constructor.
     *
     * @param iterable|Renderer[] $renderer View renderer.
     */
    public function __construct($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(View $view): bool
    {
        return !empty($this->renderer);
    }

    /**
     * {@inheritDoc}
     */
    public function render(View $view): void
    {
        foreach ($this->renderer as $renderer) {
            if ($renderer->supports($view)) {
                $renderer->render($view);
            }
        }
    }
}
