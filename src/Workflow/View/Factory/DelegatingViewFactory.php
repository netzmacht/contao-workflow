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

use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedViewContentType;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Flow\Item;

/**
 * Class ViewFactory
 */
final class DelegatingViewFactory implements ViewFactory
{
    /**
     * Map of view factories.
     *
     * @var array|ViewFactory[]
     */
    private $factories;

    /**
     * ViewFactory constructor.
     *
     * @param ViewFactory[]|array $factories View factories for a content type.
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnsupportedViewContentType When no factory is registered for the content type.
     */
    public function create(
        Item $item,
        $context,
        array $options = [],
        ?string $template = null,
        string $contentType = View::CONTENT_TYPE_HTML
    ): View {
        if (!isset($this->factories[$contentType])) {
            throw new UnsupportedViewContentType();
        }

        return $this->factories[$contentType]->create($item, $context, $options, $template, $contentType);
    }
}
