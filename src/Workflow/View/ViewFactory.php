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

/**
 * Class ViewFactory
 */
interface ViewFactory
{
    /**
     * Create the view.
     *
     * @param Item        $item        Workflow item.
     * @param mixed       $context     Current view context.
     * @param array       $options     View options.
     * @param string|null $template    The template.
     * @param string      $contentType The content type.
     *
     * @return View
     */
    public function create(
        Item $item,
        $context,
        array $options = [],
        ?string $template = null,
        string $contentType = View::CONTENT_TYPE_HTML
    ): View;
}
