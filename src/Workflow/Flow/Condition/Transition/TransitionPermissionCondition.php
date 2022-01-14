<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

final class TransitionPermissionCondition implements Condition
{
    /**
     * Authorization checker.
     *
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * Default value.
     *
     * Default value is used if no permission is given.
     *
     * @var bool
     */
    protected $grantAccessByDefault;

    /**
     * @param AuthorizationChecker $authorizationChecker Authorization checker.
     * @param bool                 $grantAccessByDefault Default access value if no permission is found.
     */
    public function __construct(AuthorizationChecker $authorizationChecker, bool $grantAccessByDefault = false)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->grantAccessByDefault = $grantAccessByDefault;
    }

    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $permission = $transition->getPermission();

        if (! $this->grantAccessByDefault && $permission === null) {
            return false;
        }

        try {
            if ($this->authorizationChecker->isGranted($transition, $item)) {
                return true;
            }
        } catch (AuthenticationCredentialsNotFoundException $exception) {
            if ($this->grantAccessByDefault) {
                return true;
            }
        }

        $context->addError(
            'transition.condition.transition_permission.failed',
            [
                'transition' => $transition->getLabel(),
                'permission' => (string) $permission,
            ]
        );

        return false;
    }
}
