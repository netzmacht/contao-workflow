<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Factory;

use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\HtmlView;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Manager\Manager;
use Twig\Environment as Twig;
use Verraes\ClassFunctions\ClassFunctions;

use function is_object;
use function is_string;
use function strtolower;

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
     * @var array<string,string|array<string,string|null>|null>
     */
    private $templates;

    /**
     * View renderer.
     *
     * @var Renderer
     */
    private $renderer;

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * @param Manager                                             $manager      Workflow manager.
     * @param Twig                                                $twig         Twig template engine.
     * @param Renderer                                            $renderer     View renderer.
     * @param RequestScopeMatcher                                 $scopeMatcher Scope matcher.
     * @param array<string,string|array<string,string|null>|null> $templates    Templates.
     */
    public function __construct(
        Manager $manager,
        Twig $twig,
        Renderer $renderer,
        RequestScopeMatcher $scopeMatcher,
        array $templates = []
    ) {
        $this->manager      = $manager;
        $this->twig         = $twig;
        $this->templates    = $templates;
        $this->renderer     = $renderer;
        $this->scopeMatcher = $scopeMatcher;
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
        $workflowName = $item->getWorkflowName();
        if ($workflowName) {
            $workflow = $this->manager->getWorkflowByName($workflowName);
        } else {
            $workflow = $this->manager->getWorkflowByItem($item);
        }

        return new HtmlView(
            $item,
            $workflow,
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
     */
    private function getTemplate($context): ?string
    {
        if (is_object($context)) {
            $type = strtolower(ClassFunctions::short($context));
        } else {
            $type = 'unknown';
        }

        if (! isset($this->templates[$type])) {
            return null;
        }

        if (is_string($this->templates[$type])) {
            return $this->templates[$type];
        }

        if ($this->scopeMatcher->isBackendRequest()) {
            return $this->templates[$type]['backend'] ?? null;
        }

        if ($this->scopeMatcher->isFrontendRequest()) {
            return $this->templates[$type]['frontend'] ?? null;
        }

        return null;
    }
}
