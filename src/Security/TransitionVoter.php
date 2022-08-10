<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Exception\FlowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

use function array_pad;
use function assert;
use function explode;

/**
 * The transition voter is a security voter to evaluate access to a step for a given workflow item.
 *
 * It expects the transition passed as the attribute and the subject as the workflow item.
 */
final class TransitionVoter extends Voter
{
    /**
     * Workflow user.
     *
     * @var User
     */
    private $user;

    /** @var WorkflowManager */
    private $workflowManager;

    public function __construct(User $user, WorkflowManager $workflowManager)
    {
        $this->user            = $user;
        $this->workflowManager = $workflowManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function supports(string $attribute, $subject): bool
    {
        [$type, $transition] = array_pad(explode(':', $attribute, 2), 2, null);

        if ($type !== 'transition' || $transition === null) {
            return false;
        }

        return $subject instanceof Item;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        assert($subject instanceof Item);

        [$type, $transition] = array_pad(explode(':', $attribute, 2), 2, null);
        if ($transition === null) {
            return false;
        }

        try {
            $workflow   = $this->workflowManager->getWorkflowByItem($subject);
            $transition = $workflow->getTransition($transition);
        } catch (WorkflowException | FlowException $exception) {
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
