<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View;

use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Override;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as Twig;

final class HtmlView implements View
{
    /**
     * Workflow item.
     */
    private Item $item;

    /**
     * Workflow definition.
     */
    private Workflow $workflow;

    /**
     * Twig template engine.
     */
    private Twig $twig;

    /**
     * Template name.
     */
    private string $template = '@NetzmachtContaoWorkflow/backend/default.html.twig';

    /**
     * Template sections.
     *
     * @var array<string,array<string,mixed>>
     */
    private array $sections = [];

    /**
     * Default section templates.
     *
     * @var array<string,string|null>
     */
    private array $sectionTemplates = [];

    /**
     * Workflow context.
     */
    private Transition|Step|State|null $context;

    /**
     * Options.
     *
     * @var array<string,mixed>
     */
    private array $options;

    /**
     * View renderer.
     */
    private Renderer $renderer;

    /**
     * @param Item                       $item     The workflow item.
     * @param Workflow                   $workflow The workflow definition.
     * @param Transition|Step|State|null $context  Current context.
     * @param Renderer                   $renderer View renderer.
     * @param Twig                       $twig     The twig template engine.
     * @param string|null                $template The view template.
     * @param array<string,mixed>        $options  Options.
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        Transition|Step|State|null $context,
        Renderer $renderer,
        Twig $twig,
        string|null $template = null,
        array $options = [],
    ) {
        $this->item     = $item;
        $this->workflow = $workflow;
        $this->twig     = $twig;
        $this->context  = $context;
        $this->options  = $options;
        $this->renderer = $renderer;

        if ($template === null) {
            return;
        }

        $this->template = $template;
    }

     /** {@inheritDoc} */
    #[Override]
    public function getContext(): Transition|Step|State|null
    {
        return $this->context;
    }

    #[Override]
    public function getItem(): Item
    {
        return $this->item;
    }

    #[Override]
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    #[Override]
    public function getContentType(): string
    {
        return self::CONTENT_TYPE_HTML;
    }

     /** {@inheritDoc} */
    #[Override]
    public function getOption(string $name, $default = null): mixed
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return $default;
    }

     /** {@inheritDoc} */
    #[Override]
    public function addSection(string $name, array $parameters, string|null $defaultTemplate = null): View
    {
        $this->sections[$name]         = $parameters;
        $this->sectionTemplates[$name] = $defaultTemplate;

        return $this;
    }

    #[Override]
    public function hasSection(string $name): bool
    {
        return isset($this->sections[$name]);
    }

    /** {@inheritDoc} */
    #[Override]
    public function render(callable|null $viewRenderer = null): Response
    {
        if ($this->renderer->supports($this)) {
            $this->renderer->render($this);
        }

        $renderer = $viewRenderer ?? $this->twigRenderer();

        return $renderer(
            $this->template,
            [
                'item'      => $this->item,
                'workflow'  => $this->workflow,
                'sections'  => new Sections($this->sections, $this->sectionTemplates),
            ]
        );
    }

    /** @return callable(string, array<string, mixed>): Response */
    private function twigRenderer(): callable
    {
        return function (string $template, array $parameters): Response {
            $buffer   = $this->twig->render($this->template, $parameters);
            $response = new Response($buffer);
            $response->headers->set('Content-Type', $this->getContentType());

            return $response;
        };
    }
}
