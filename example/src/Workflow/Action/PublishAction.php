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

namespace Netzmacht\ContaoWorkflowExampleBundle\Workflow\Action;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractPropertyAccessAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

final class PublishAction extends AbstractPropertyAccessAction
{
    /**
     * Published state.
     *
     * @var string
     */
    private $state;

    /**
     * @inheritDoc
     */
    public function __construct(
        PropertyAccessManager $propertyAccessManager,
        string $name,
        string $label,
        string $state,
        array $config = []
    ) {
        parent::__construct($propertyAccessManager, $name, $label, $config);

        $this->state = $state;
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
     */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $entity = $item->getEntity();

        // The property access manager is a generic solution to support different entity types.
        // If you have an contao model set up for the provider name, your entity should be the model
        // You can directly access the model, if your action is limited to the specific entity type
        if (!$this->propertyAccessManager->supports($entity)) {
            throw new UnsupportedEntity('Entity not supported');
        }

        $accessor = $this->propertyAccessManager->provideAccess($entity);
        $accessor->set('published', $this->state);
    }
}
