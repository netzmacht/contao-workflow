<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

namespace spec\Netzmacht\ContaoWorkflowBundle\Security;

use Contao\BackendUser;
use Netzmacht\ContaoWorkflowBundle\Security\StepPermissionVoter;
use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\Step;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StepPermissionVoterSpec extends ObjectBehavior
{
    function let(TokenInterface $token, BackendUser $user)
    {
        $token
            ->getUser()
            ->willReturn($user);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StepPermissionVoter::class);
    }

    function it_grants_access_for_granted_step_permission(Step $step, TokenInterface $token, BackendUser $user)
    {
        $permission = Permission::forWorkflowName('test', 'permission');

        $step
            ->hasPermission($permission)
            ->willReturn(true);

        $user
            ->hasAccess((string) $permission, 'workflow')
            ->willReturn(true);

        $this
            ->vote($token, $step, [$permission])
            ->shouldReturn(Voter::ACCESS_GRANTED);
    }

    function it_abstains_access_for_granted_non_step_permission(Step $step, TokenInterface $token, BackendUser $user)
    {
        $permission = Permission::forWorkflowName('test', 'permission');

        $step
            ->hasPermission($permission)
            ->willReturn(false);

        $user
            ->hasAccess((string) $permission, 'workflow')
            ->willReturn(true);

        $this
            ->vote($token, $step, [$permission])
            ->shouldReturn(Voter::ACCESS_ABSTAIN);
    }

    function it_denies_access_for_non_granted_step_permission(Step $step, TokenInterface $token, BackendUser $user)
    {
        $permission = Permission::forWorkflowName('test', 'permission');

        $step
            ->hasPermission($permission)
            ->willReturn(true);

        $user
            ->hasAccess((string) $permission, 'workflow')
            ->willReturn(false);

        $this
            ->vote($token, $step, [$permission])
            ->shouldReturn(Voter::ACCESS_DENIED);
    }
}
