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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

/**
 * Class PermissionCondition
 *
 * @package Netzmacht\ContaoWorkflowBundle\Condition\Transition
 */
final class StepPermissionCondition implements Condition
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
     * If a workflow is not started it does not have a current step. So you can decide if it should be allowed.
     *
     * @var bool
     */
    private $allowStartTransition;

    /**
     * PermissionCondition constructor.
     *
     * @param AuthorizationChecker $authorizationChecker Authorization checker.
     * @param bool                 $grantAccessByDefault Default access value if no permission is found.
     * @param bool                 $allowStartTransition Allow start transition.
     */
    public function __construct(
        AuthorizationChecker $authorizationChecker,
        bool $grantAccessByDefault = false,
        bool $allowStartTransition = true
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->grantAccessByDefault = $grantAccessByDefault;
        $this->allowStartTransition = $allowStartTransition;
    }

    /**
     * {@inheritDoc}
     */
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        // workflow is not started, so no start step exists
        if (!$item->isWorkflowStarted()) {
            if ($this->allowStartTransition) {
                return true;
            }

            $context->addError('transition.condition.step_permission_failed.not_started');

            return false;
        }

        $stepName   = $item->getCurrentStepName();
        $step       = $transition->getWorkflow()->getStep($stepName);
        $permission = $step->getPermission();

        if (!$this->grantAccessByDefault && $permission === null) {
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
            'transition.condition.step_permission.failed',
            [
                '%step%'       => $item->getCurrentStepName(),
                '%permission%' => $permission ? ((string) $permission) : '-',
            ]
        );

        return false;
    }
}
