<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Exception\FlowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * The transition voter is a security voter to evaluate access to a step for a given workflow item.
 *
 * It expects the transition passed as the attribute and the subject as the workflow item.
 *
 * @extends Voter<string, Item>
 */
final class TransitionVoter extends Voter
{
    public function __construct(private readonly User $user, private readonly WorkflowManager $workflowManager)
    {
    }

     /** {@inheritDoc} */
    #[Override]
    protected function supports(string $attribute, $subject): bool
    {
        if (! WorkflowPermissions::isTransitTransition($attribute)) {
            return false;
        }

        return $subject instanceof Item;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    #[Override]
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $transition = WorkflowPermissions::extractTransitionName($attribute);

        try {
            $workflow   = $this->workflowManager->getWorkflowByItem($subject);
            $transition = $workflow->getTransition($transition);
        } catch (WorkflowException | FlowException) {
            return false;
        }

        $permission = $transition->getPermission();
        if ($permission === null) {
            return true;
        }

        $user = $token->getUser();
        if (! $user instanceof UserInterface) {
            $user = null;
        }

        return $this->user->hasPermission($permission, $user);
    }
}
