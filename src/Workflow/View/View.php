<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View;

use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\HttpFoundation\Response;

interface View
{
    public const string CONTENT_TYPE_HTML = 'text/html';
    public const string CONTENT_TYPE_JSON = 'application/json';

    /**
     * Get workflow context.
     */
    public function getContext(): Transition|Step|State;

    /**
     * Get the workflow item.
     */
    public function getItem(): Item;

    /**
     * Get the workflow definition.
     */
    public function getWorkflow(): Workflow;

    /**
     * Get the output format.
     */
    public function getContentType(): string;

    /**
     * Get an option.
     *
     * @param string $name    The option name.
     * @param null   $default Default option value.
     */
    public function getOption(string $name, $default = null): mixed;

    /**
     * Add a section.
     *
     * @param string              $name            The section name.
     * @param array<string,mixed> $parameters      The section parameters.
     * @param string|null         $defaultTemplate The default template.
     */
    public function addSection(string $name, array $parameters, string|null $defaultTemplate = null): View;

    /**
     * Check is section exists.
     *
     * @param string $name The section name.
     */
    public function hasSection(string $name): bool;

    /**
     * Render the response.
     */
    public function render(): Response;
}
