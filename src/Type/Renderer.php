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

namespace Netzmacht\Contao\Workflow\Type;

use Netzmacht\Workflow\Flow\Item;

/**
 * Interface Renderer
 *
 * @package Netzmacht\Contao\Workflow\Type
 */
interface Renderer
{
    /**
     * Render the label.
     *
     * @param Item  $item    The workflow item.
     * @param array $context Optional context data.
     *
     * @return string
     */
    public function renderLabel(Item $item, array $context = []): string;

    /**
     * Render the default view. Could be an alias for either teaser or detail view.
     *
     * @param Item  $item    The workflow item.
     * @param array $context Optional context data.
     *
     * @return string
     */
    public function renderDefaultView(Item $item, array $context = []): string;

    /**
     * Render a short teaser view.
     *
     * @param Item  $item    The workflow item.
     * @param array $context Optional context data.
     *
     * @return string
     */
    public function renderTeaserView(Item $item, array $context = []): string;

    /**
     * Render the detail view.
     *
     * @param Item  $item    The workflow item.
     * @param array $context Optional context data.
     *
     * @return string
     */
    public function renderDetailView(Item $item, array $context = []): string;
}
