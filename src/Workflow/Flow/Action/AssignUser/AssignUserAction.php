<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AssignUser;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Security\User;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractPropertyAccessAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class AssignUserAction assigns a user to an entity property.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class AssignUserAction extends AbstractPropertyAccessAction
{
    /**
     * If true the current user is assigned.
     *
     * @var bool
     */
    private $assignCurrentUser;

    /**
     * The workflow user.
     *
     * @var User
     */
    private $user;

    /**
     * The property name of an entity property which is used for the user assignment.
     *
     * @var string
     */
    private $property;

    /**
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param User                  $user                  Workflow user.
     * @param string                $name                  The action name.
     * @param bool                  $assignCurrentUser     If true the current user is assigned.
     * @param string                $property              The entity property storing the user information.
     * @param string                $label                 The action label.
     * @param array<string,mixed>   $config                The action configuration.
     */
    public function __construct(
        PropertyAccessManager $propertyAccessManager,
        User $user,
        string $name,
        bool $assignCurrentUser,
        string $property,
        string $label = '',
        array $config = []
    ) {
        parent::__construct($propertyAccessManager, $name, $label, $config);

        $this->user              = $user;
        $this->assignCurrentUser = $assignCurrentUser;
        $this->property          = $property;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        if ($this->assignCurrentUser) {
            return [];
        }

        return [$this->getName() . '_user'];
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function validate(Item $item, Context $context): bool
    {
        if ($this->assignCurrentUser) {
            return true;
        }

        return $context->getPayload()->has($this->getName() . '_user');
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $accessor = $this->propertyAccessManager->provideAccess($item->getEntity());
        $userId   = null;

        if ($this->assignCurrentUser) {
            $userId = $this->user->getUserId();
            $userId = $userId ? (string) $userId : null;
        } else {
            $userId = $context->getPayload()->get($this->getName() . '_user');
        }

        $accessor->set($this->property, $userId);
    }

    /**
     * Detect if current user is assigned or a user should be chosen.
     */
    public function isCurrentUserAssigned(): bool
    {
        return $this->assignCurrentUser;
    }
}
