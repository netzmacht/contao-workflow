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
 * Class DefaultRenderer
 *
 * @package Netzmacht\Contao\Workflow\Type
 */
class DefaultRenderer implements Renderer
{
    /**
     * {@inheritDoc}
     */
    public function renderLabel(Item $item, array $context = []): string
    {
        return (string) $item->getEntityId();
    }

    /**
     * {@inheritDoc}
     */
    public function renderDefaultView(Item $item, array $context = []): string
    {
        return $this->renderLabel($item, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function renderTeaserView(Item $item, array $context = []): string
    {
        return $this->renderLabel($item, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function renderDetailView(Item $item, array $context = []): string
    {
        return $this->renderLabel($item, $context);
    }
}
