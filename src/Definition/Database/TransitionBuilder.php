<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Workflow\Definition\Database;

use Netzmacht\Contao\Workflow\Condition\Transition\TransitionPermissionCondition;
use Netzmacht\Contao\Workflow\Definition\Event\CreateTransitionEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as AuthorizationChecker;

/**
 * Class PermissionSubscriber integrates default permission features.
 *
 * @package Netzmacht\Contao\Workflow\Definition\Database
 */
class TransitionBuilder
{
    /**
     * Authorization checker.
     *
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * PermissionSubscriber constructor.
     *
     * @param AuthorizationChecker $authorizationChecker Authorization checker.
     */
    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Add default transition conditions.
     *
     * @param CreateTransitionEvent $event The subscribed event.
     *
     * @return void
     */
    public function addTransitionConditions(CreateTransitionEvent $event)
    {
        $transition = $event->getTransition();
        $transition->addPreCondition(
            new TransitionPermissionCondition($this->authorizationChecker, true)
        );
    }
}
