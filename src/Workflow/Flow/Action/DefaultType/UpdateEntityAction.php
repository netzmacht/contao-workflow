<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\DefaultType;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractPropertyAccessAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class UpdateEntityAction updates the entity of a default workflow item
 */
final class UpdateEntityAction extends AbstractPropertyAccessAction
{
    /**
     * Construct.
     *
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     */
    public function __construct(PropertyAccessManager $propertyAccessManager)
    {
        parent::__construct($propertyAccessManager, 'Update entity action');
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function validate(Item $item, Context $context): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ActionFailedException When no property access is given.
     */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $entity = $item->getEntity();
        if (!$this->propertyAccessManager->supports($entity)) {
            throw new ActionFailedException('No property access to entity');
        }

        $accessor = $this->propertyAccessManager->provideAccess($entity);
        $accessor->set('workflowStep', $item->getCurrentStepName());
    }
}
