<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form\Builder;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Additional interface for action form builders.
 *
 * Use it form action form builders which might to enrich the default form data.
 */
interface DataAwareActionFormBuilder extends ActionFormBuilder
{
    /**
     * Build the form data and return the changed data.
     *
     * @param Action     $action     The action the form belongs to.
     * @param Transition $transition The transition being build.
     * @param Context    $context    The workflow context.
     * @param Item       $item       The current workflow item.
     * @param array      $data       Default form data.
     *
     * @return array
     */
    public function buildFormData(
        Action $action,
        Transition $transition,
        Context $context,
        Item $item,
        array $data
    ): array;
}
