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

use Netzmacht\ContaoWorkflowBundle\Workflow\Manager\Manager;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\History\HistoryRenderer;
use Netzmacht\Workflow\Flow\Item;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as TemplateEngine;

/**
 * Class ViewFactory
 */
final class ViewFactory
{
    /**
     * Workflow manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Template engine.
     *
     * @var TemplateEngine
     */
    private $engine;

    /**
     * History renderer.
     *
     * @var HistoryRenderer
     */
    private $historyRenderer;

    /**
     * ViewFactory constructor.
     *
     * @param Manager         $manager         Workflow manager.
     * @param TemplateEngine  $engine          Template engine.
     * @param HistoryRenderer $historyRenderer History renderer.
     */
    public function __construct(Manager $manager, TemplateEngine $engine, HistoryRenderer $historyRenderer)
    {
        $this->manager         = $manager;
        $this->engine          = $engine;
        $this->historyRenderer = $historyRenderer;
    }

    /**
     * Create the view.
     *
     * @param Item        $item        Workflow item.
     * @param string|null $template    The template.
     * @param string      $contentType The content type.
     *
     * @return View
     */
    public function create(Item $item, string $template = null, string $contentType = View::CONTENT_TYPE_HTML): View
    {
        $workflow = $this->manager->getWorkflowByName($item->getWorkflowName());

        return new View(
            $item,
            $workflow,
            $this->engine,
            $this->historyRenderer,
            $template,
            $contentType
        );
    }
}
