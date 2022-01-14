<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

use function assert;

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

    /**
     * @param User $user Workflow user.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($attribute, $subject): bool
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType - TODO: Do we need to fix the attribute type */
        if (! $attribute instanceof Transition) {
            return false;
        }

        return $subject instanceof Item;
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @psalm-suppress DocblockTypeContradiction - TODO: Do we need to fix the attribute type */
        assert($attribute instanceof Transition);
        assert($subject instanceof Item);

        $permission = $attribute->getPermission();
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
