<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AssignUser;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Security\User;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Override;

/**
 * Class AssignUserActionFactory creates an AssignUserAction
 *
 * The provider has to configure assignable user fields to support this action.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class AssignUserActionFactory implements ActionTypeFactory
{
    /**
     * @param PropertyAccessManager             $propertyAccessManager The property access manager.
     * @param User                              $user                  The workflow user.
     * @param array<string,array<string,mixed>> $providerConfiguration The provider configuration.
     */
    public function __construct(
        private PropertyAccessManager $propertyAccessManager,
        private User $user,
        private array $providerConfiguration,
    ) {
    }

    #[Override]
    public function getCategory(): string
    {
        return 'default';
    }

    #[Override]
    public function getName(): string
    {
        return 'assign_user';
    }

    #[Override]
    public function isPostAction(): bool
    {
        return false;
    }

    #[Override]
    public function supports(Workflow $workflow): bool
    {
        return $this->providerConfiguration[$workflow->getProviderName()]['assign_users'] !== [];
    }

     /** {@inheritDoc} */
    #[Override]
    public function create(array $config, Transition $transition): Action
    {
        return new AssignUserAction(
            $this->propertyAccessManager,
            $this->user,
            'action_' . $config['id'],
            (bool) $config['assign_user_current_user'],
            (string) $config['assign_user_property'],
            $config['label'],
            $config,
        );
    }
}
