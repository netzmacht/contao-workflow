<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Security\User;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

final class AssignedUserCondition implements Condition
{
    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * The workflow user.
     *
     * @var User
     */
    private $user;

    /**
     * User assignment property.
     *
     * @var string
     */
    private $property;

    /**
     * {@inheritDoc}
     */
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $entity = $item->getEntity();
        if (! $this->propertyAccessManager->supports($entity)) {
            return false;
        }

        $accessor     = $this->propertyAccessManager->provideAccess($entity);
        $assignedUser = $accessor->get($this->property);
        if ($assignedUser === null) {
            return false;
        }

        $userId = $this->user->getUserId();

        return EntityId::fromString($assignedUser)->equals($userId);
    }
}