<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Factory;

use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedViewContentType;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Flow\Item;

final class DelegatingViewFactory implements ViewFactory
{
    /**
     * Map of view factories.
     *
     * @var array|ViewFactory[]
     */
    private $factories;

    /**
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
        if (! isset($this->factories[$contentType])) {
            throw new UnsupportedViewContentType();
        }

        return $this->factories[$contentType]->create($item, $context, $options, $template, $contentType);
    }
}
