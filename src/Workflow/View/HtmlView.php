<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View;

use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as Twig;

final class HtmlView implements View
{
    /**
     * Workflow item.
     *
     * @var Item
     */
    private $item;

    /**
     * Workflow definition.
     *
     * @var Workflow
     */
    private $workflow;

    /**
     * Twig template engine.
     *
     * @var Twig
     */
    private $twig;

    /**
     * Template name.
     *
     * @var string
     */
    private $template = '@NetzmachtContaoWorkflow/backend/default.html.twig';

    /**
     * Template sections.
     *
     * @var array<string,array<string,mixed>>
     */
    private $sections = [];

    /**
     * Default section templates.
     *
     * @var array<string,string|null>
     */
    private $sectionTemplates = [];

    /**
     * Workflow context.
     *
     * @var Transition|Step|State
     */
    private $context;

    /**
     * Options.
     *
     * @var array<string,mixed>
     */
    private $options;

    /**
     * View renderer.
     *
     * @var Renderer
     */
    private $renderer;

    /**
     * @param Item                  $item     The workflow item.
     * @param Workflow              $workflow The workflow definition.
     * @param Transition|Step|State $context  Current context.
     * @param Renderer              $renderer View renderer.
     * @param Twig                  $twig     The twig template engine.
     * @param string|null           $template The view template.
     * @param array<string,mixed>   $options  Options.
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        $context,
        Renderer $renderer,
        Twig $twig,
        ?string $template = null,
        array $options = []
    ) {
        $this->item     = $item;
        $this->workflow = $workflow;
        $this->twig     = $twig;
        $this->context  = $context;
        $this->options  = $options;
        $this->renderer = $renderer;

        if (! $template) {
            return;
        }

        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    public function getContentType(): string
    {
        return self::CONTENT_TYPE_HTML;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption(string $name, $default = null)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function addSection(string $name, array $parameters, ?string $defaultTemplate = null): View
    {
        $this->sections[$name]         = $parameters;
        $this->sectionTemplates[$name] = $defaultTemplate;

        return $this;
    }

    public function hasSection(string $name): bool
    {
        return isset($this->sections[$name]);
    }

    public function render(): Response
    {
        if ($this->renderer->supports($this)) {
            $this->renderer->render($this);
        }

        $buffer = $this->twig->render(
            $this->template,
            [
                'item'      => $this->item,
                'workflow'  => $this->workflow,
                'sections'  => new Sections($this->sections, $this->sectionTemplates),
            ]
        );

        $response = new Response($buffer);
        $response->headers->set('Content-Type', $this->getContentType());

        return $response;
    }
}
