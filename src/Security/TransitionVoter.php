<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use function assert;

/**
 * The transition voter is a security voter to evaluate access to a step for a given workflow item.
 *
 * It expects the transition passed as the attribute and the subject as the workflow item.
 */
final class TransitionVoter extends Voter
{
    /** @var User */
    private $user;

    /**
     * {@inheritDoc}
     */
    protected function supports($attribute, $subject): bool
    {
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
        assert($attribute instanceof Transition);
        assert($subject instanceof Item);

        $permission = $attribute->getPermission();
        if ($permission === null) {
            return true;
        }

        return $this->user->hasPermission($permission);
    }
}
