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

use Netzmacht\ContaoWorkflowBundle\Workflow\View\History\HistoryRenderer;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class View
 */
final class View
{
    const CONTENT_TYPE_HTML = 'text/html';

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
     * Template engine.
     *
     * @var TemplateEngine
     */
    private $engine;

    /**
     * Template name.
     *
     * @var string
     */
    private $template = '@NetzmachtContaoWorkflow/backend/step.html.twig';

    /**
     * Output format.
     *
     * @var string
     */
    private $contentType;

    /**
     * Template sections.
     *
     * @var array|string[]
     */
    private $sections = [];

    /**
     * History renderer.
     *
     * @var HistoryRenderer
     */
    private $historyRenderer;

    /**
     * HtmlStepView constructor.
     *
     * @param Item            $item
     * @param Workflow        $workflow
     * @param TemplateEngine  $engine
     * @param HistoryRenderer $historyRenderer
     * @param string|null     $template
     * @param string          $contentType
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        TemplateEngine $engine,
        HistoryRenderer $historyRenderer,
        string $template = null,
        string $contentType = self::CONTENT_TYPE_HTML
    ) {
        $this->item            = $item;
        $this->workflow        = $workflow;
        $this->engine          = $engine;
        $this->historyRenderer = $historyRenderer;
        $this->contentType     = $contentType;

        if ($template) {
            $this->template = $template;
        }
    }

    /**
     * Get the workflow item.
     *
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * Get the workflow definition.
     *
     * @return Workflow
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    /**
     * Get the output format.
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Add a section.
     *
     * @param string $name    The section name.
     * @param string $content The section content.
     *
     * @return View
     */
    public function addSection(string $name, string $content): View
    {
        $this->sections[$name] = $content;

        return $this;
    }

    /**
     * Render the response.
     *
     * @return Response
     */
    public function render(): Response
    {
        $history = [];

        if ($this->historyRenderer->supports($this->item, $this->workflow)) {
            $history = $this->historyRenderer->render($this->item, $this->workflow, $history);
        }

        $response = $this->engine->renderResponse(
            $this->template,
            [
                'item'     => $this->item,
                'workflow' => $this->workflow,
                'sections' => $this->sections,
                'history'  => $history,
            ]
        );

        $response->headers->set('Content-Type', $this->getContentType());

        return $response;
    }
}
