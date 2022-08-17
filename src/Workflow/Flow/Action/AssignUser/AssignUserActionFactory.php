<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AssignUser;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Security\User;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class AssignUserActionFactory creates an AssignUserAction
 *
 * The provider has to configured assignable user fields to support this action.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AssignUserActionFactory implements ActionTypeFactory
{
    /**
     * The property access manager.
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
     * Provider configuration.
     *
     * @var array<string,array<string,mixed>>
     */
    private $providerConfiguration;

    /**
     * @param PropertyAccessManager             $propertyAccessManager The property access manager.
     * @param User                              $user                  The workflow user.
     * @param array<string,array<string,mixed>> $providerConfiguration The provider configuration.
     */
    public function __construct(PropertyAccessManager $propertyAccessManager, User $user, array $providerConfiguration)
    {
        $this->propertyAccessManager = $propertyAccessManager;
        $this->user                  = $user;
        $this->providerConfiguration = $providerConfiguration;
    }

    public function getCategory(): string
    {
        return 'default';
    }

    public function getName(): string
    {
        return 'assign_user';
    }

    public function isPostAction(): bool
    {
        return false;
    }

    public function supports(Workflow $workflow): bool
    {
        return $this->providerConfiguration[$workflow->getProviderName()]['assign_users'] !== [];
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config, Transition $transition): Action
    {
        return new AssignUserAction(
            $this->propertyAccessManager,
            $this->user,
            'action_' . $config['id'],
            (bool) $config['assign_user_current_user'],
            (string) $config['assign_user_property'],
            $config['label'],
            $config
        );
    }
}
