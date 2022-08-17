<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\Workflow\Flow\Security\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface as Token;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Throwable;

/**
 * @deprecated Deprecated since version 2.3.0 and will be removed in version 3.0.0.
 */
abstract class AbstractPermissionVoter extends Voter
{
    /**
     * The workflow user.
     *
     * @var User
     */
    private $user;

    /**
     * @param User $user The workflow user.
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
        $subjectClass = $this->getSubjectClass();

        if (! $subject instanceof $subjectClass) {
            return false;
        }

        if ($attribute instanceof Permission) {
            $permission = $attribute;
        } else {
            try {
                $permission = Permission::fromString($attribute);
            } catch (Throwable $e) {
                return false;
            }
        }

        return $subject->hasPermission($permission);
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute($attribute, $subject, Token $token)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType - TODO: Do we need to fix the attribute type */
        if (! $attribute instanceof Permission) {
            return false;
        }

        return $this->user->hasPermission($attribute);
    }

    /**
     * Get the expected subject class.
     */
    abstract protected function getSubjectClass(): string;
}
