<?php

/**
 * contao-workflow.
 *
 * @package    contao-workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Workflow\Security;

use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Security\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface as AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WorkflowPermissionVoter extends Voter
{
    /**
     * @var AccessDecisionManager
     */
    private $decisionManager;

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof Transition) {
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
     * @param string         $attribute
     * @param Transition      $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $permission = Permission::fromString($attribute);

        if ($subject->hasPermission($permission)) {
            return true;
        }

        if ($this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

    }
}
