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

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\Workflow\Flow\Security\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface as Token;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AbstractPermissionVoter
 *
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
     * AbstractPermissionVoter constructor.
     *
     * @param User $user The workflow user.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($attribute, $subject)
    {
        $subjectClass = $this->getSubjectClass();

        if (!$subject instanceof $subjectClass) {
            return false;
        }

        if ($attribute instanceof Permission) {
            $permission = $attribute;
        } else {
            try {
                $permission = Permission::fromString((string) $attribute);
            } catch (\Exception $e) {
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
        if (! $attribute instanceof Permission) {
            return false;
        }

        return $this->user->hasPermission($attribute);
    }

    /**
     * Get the expected subject class.
     *
     * @return string
     */
    abstract protected function getSubjectClass(): string;
}
