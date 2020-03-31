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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Factory;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\HtmlView;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Manager\Manager;
use Twig\Environment as Twig;
use Verraes\ClassFunctions\ClassFunctions;

/**
 * Class HtmlViewFactory
 */
final class HtmlViewFactory implements ViewFactory
{
    /**
     * Workflow manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Twig template engine.
     *
     * @var Twig
     */
    private $twig;

    /**
     * Map of context and template.
     *
     * @var array
     */
    private $templates;

    /**
     * View renderer.
     *
     * @var Renderer
     */
    private $renderer;

    /**
     * HtmlViewFactory constructor.
     *
     * @param Manager  $manager   Workflow manager.
     * @param Twig     $twig      Twig template engine.
     * @param Renderer $renderer  View renderer.
     * @param array    $templates Templates.
     */
    public function __construct(Manager $manager, Twig $twig, Renderer $renderer, array $templates = [])
    {
        $this->manager   = $manager;
        $this->twig      = $twig;
        $this->templates = $templates;
        $this->renderer  = $renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function create(
        Item $item,
        $context,
        array $options = [],
        ?string $template = null,
        string $contentType = View::CONTENT_TYPE_HTML
    ): View {
        return new HtmlView(
            $item,
            $this->manager->getWorkflowByName($item->getWorkflowName()),
            $context,
            $this->renderer,
            $this->twig,
            $template ?: $this->getTemplate($context),
            $options
        );
    }

    /**
     * Get the template for the view.
     *
     * @param mixed $context Given context.
     *
     * @return null|string
     */
    private function getTemplate($context): ?string
    {
        $type = strtolower(ClassFunctions::short($context));

        if (isset($this->templates[$type])) {
            return $this->templates[$type];
        }

        return null;
    }
}
