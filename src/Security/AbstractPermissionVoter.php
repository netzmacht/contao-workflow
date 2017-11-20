<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Workflow\Security;

use Contao\BackendUser;
use Contao\FrontendUser;
use Netzmacht\Workflow\Security\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface as Token;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AbstractPermissionVoter
 *
 * @package Netzmacht\Contao\Workflow\Security
 */
abstract class AbstractPermissionVoter extends Voter
{
    /**
     * {@inheritDoc}
     */
    protected function supports($attribute, $subject)
    {
        $subjectClass = $this->getSubjectClass();

        if (!$subject instanceof $subjectClass) {
            return false;
        }

        try {
            $permission = Permission::fromString((string) $subject);
        } catch (\Exception $e) {
            return false;
        }

        return $subject->hasPermission($permission);
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute($attribute, $subject, Token $token)
    {
        $user = $token->getUser();

        // Only Contao users are supported.
        if (!$user instanceof FrontendUser && !$user instanceof BackendUser) {
            return false;
        }

        if ($user->hasAccess($attribute, 'workflow')) {
            return true;
        }

        return false;
    }

    /**
     * Get the expected subject class.
     *
     * @return string
     */
    abstract protected function getSubjectClass(): string;
}
