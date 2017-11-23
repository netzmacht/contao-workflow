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

namespace Netzmacht\Contao\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as AuthorizationChecker;

/**
 * Class PermissionCondition
 *
 * @package Netzmacht\Contao\Workflow\Condition\Transition
 */
class TransitionPermissionCondition implements Condition
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
     * PermissionCondition constructor.
     *
     * @param AuthorizationChecker $authorizationChecker Authorization checker.
     * @param bool                 $grantAccessByDefault Default access value if no permission is found.
     */
    public function __construct(AuthorizationChecker $authorizationChecker, bool $grantAccessByDefault = false)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->grantAccessByDefault = $grantAccessByDefault;
    }

    /**
     * {@inheritDoc}
     */
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $permission = $transition->getPermission();

        if ($permission === null) {
            if ($this->grantAccessByDefault) {
                return true;
            }
        } elseif ($this->authorizationChecker->isGranted($permission, $transition)) {
            return true;
        }

        $context->addError('transition.condition.transition_permission_failed', array((string) $permission));

        return false;
    }
}
