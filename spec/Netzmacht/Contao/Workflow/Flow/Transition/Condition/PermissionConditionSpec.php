<?php

namespace spec\Netzmacht\Contao\Workflow\Flow\Transition\Condition;

use Netzmacht\Contao\Workflow\Acl\AclManager;
use Netzmacht\Contao\Workflow\Acl\Role;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class_alias('spec\Netzmacht\Contao\Workflow\Flow\Transition\Condition\BackendUser', 'BackendUser');

class PermissionConditionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Flow\Transition\Condition\PermissionCondition');

    }

    public function let(AclManager $aclManager)
    {
        $aclManager->hasAdminPermissions()->willReturn(false);

        $this->beConstructedWith($aclManager);
    }

    public function it_can_ignore_admin_permission()
    {
        $this->isAdminPermissionIgnored()->shouldReturn(false);
        $this->ignoreAdminPermission(true)->shouldReturn($this);
        $this->isAdminPermissionIgnored()->shouldReturn(true);
    }

    public function it_matches_if_user_is_admin(Transition $transition, Workflow $workflow, Entity $entity, Context $context, AclManager $aclManager, Role $role)
    {
        $aclManager->getRoles($workflow)->willReturn();
        $aclManager->hasAdminPermissions()->willReturn(true);

        $transition->getRoles()->willReturn(array($role));

        $this->match($transition, $entity, $context)->shouldReturn(true);
    }

    public function it_does_not_match_if_user_is_admin_and_admin_permission_is_ignored(Transition $transition, Workflow $workflow, Entity $entity, Context $context, AclManager $aclManager, Role $role)
    {
        $aclManager->getRoles($workflow)->willReturn(array($role));

        $transition->getRoles()->willReturn(array($role));

        $this->ignoreAdminPermission(true);

        $this->match($transition, $entity, $context)->shouldReturn(false);
    }

    public function it_matches_if_user_has_role(Transition $transition, Workflow $workflow, Entity $entity, Context $context, AclManager $aclManager, Role $role)
    {
        $aclManager->getRoles($workflow)->willReturn(array($role));
        $transition->getRoles()->willReturn(array($role));

        $this->match($transition, $entity, $context)->shouldReturn(false);
    }

    public function it_does_not_match_if_user_has_not_the_role(Transition $transition, Workflow $workflow, Entity $entity, Context $context, AclManager $aclManager, Role $role)
    {
        $aclManager->getRoles($workflow)->willReturn();
        $transition->getRoles()->willReturn(array($role));

        $this->match($transition, $entity, $context)->shouldReturn(false);
    }
}

class BackendUser
{
    public $isAdmin;

    public $groups;
}
