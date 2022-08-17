<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View;

use Netzmacht\Workflow\Flow\Item;

interface ViewFactory
{
    /**
     * Create the view.
     *
     * @param Item                $item        Workflow item.
     * @param mixed               $context     Current view context.
     * @param array<string,mixed> $options     View options.
     * @param string|null         $template    The template.
     * @param string              $contentType The content type.
     */
    public function create(
        Item $item,
        $context,
        array $options = [],
        ?string $template = null,
        string $contentType = View::CONTENT_TYPE_HTML
    ): View;
}
