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

use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class View
 */
interface View
{
    const CONTENT_TYPE_HTML = 'text/html';

    /**
     * Get workflow context.
     *
     * @return Transition|Step|State
     */
    public function getContext();

    /**
     * Get the workflow item.
     *
     * @return Item
     */
    public function getItem(): Item;

    /**
     * Get the workflow definition.
     *
     * @return Workflow
     */
    public function getWorkflow(): Workflow;

    /**
     * Get the output format.
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Add a section.
     *
     * @param string      $name            The section name.
     * @param array       $parameters      The section parameters.
     * @param string|null $defaultTemplate The default template.
     *
     * @return View
     */
    public function addSection(string $name, array $parameters, ?string $defaultTemplate = null): View;

    /**
     * Check is section exists.
     *
     * @param string $name The section name.
     *
     * @return bool
     */
    public function hasSection(string $name): bool;

    /**
     * Render the response.
     *
     * @return Response
     */
    public function render(): Response;
}
