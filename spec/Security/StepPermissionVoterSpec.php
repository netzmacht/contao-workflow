<?php

declare(strict_types=1);

namespace spec\Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\ContaoWorkflowBundle\Security\StepPermissionVoter;
use Netzmacht\ContaoWorkflowBundle\Security\User;
use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\Step;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class StepPermissionVoterSpec extends ObjectBehavior
{
    public function let(User $user): void
    {
        $this->beConstructedWith($user);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(StepPermissionVoter::class);
    }

    public function it_grants_access_for_granted_step_permission(Step $step, TokenInterface $token, User $user): void
    {
        $permission = Permission::forWorkflowName('test', 'permission');

        $step
            ->hasPermission($permission)
            ->willReturn(true);

        $user
            ->hasPermission($permission)
            ->willReturn(true);

        $this
            ->vote($token, $step, [$permission])
            ->shouldReturn(Voter::ACCESS_GRANTED);
    }

    public function it_abstains_access_for_granted_non_step_permission(
        Step $step,
        TokenInterface $token,
        User $user
    ): void {
        $permission = Permission::forWorkflowName('test', 'permission');

        $step
            ->hasPermission($permission)
            ->willReturn(false);

        $user
            ->hasPermission($permission)
            ->willReturn(true);

        $this
            ->vote($token, $step, [$permission])
            ->shouldReturn(Voter::ACCESS_ABSTAIN);
    }

    public function it_denies_access_for_non_granted_step_permission(
        Step $step,
        TokenInterface $token,
        User $user
    ): void {
        $permission = Permission::forWorkflowName('test', 'permission');

        $step
            ->hasPermission($permission)
            ->willReturn(true);

        $user
            ->hasPermission($permission)
            ->willReturn(false);

        $this
            ->vote($token, $step, [$permission])
            ->shouldReturn(Voter::ACCESS_DENIED);
    }
}
