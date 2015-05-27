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

namespace Netzmacht\Workflow\Contao\Definition\Database;

use Netzmacht\Workflow\Contao\Backend\Event\GetWorkflowPermissionsEvent;
use Netzmacht\Workflow\Contao\Condition\Transition\ContaoTransitionPermissionCondition;
use Netzmacht\Workflow\Contao\Definition\Event\CreateTransitionEvent;
use Netzmacht\Workflow\Contao\ServiceContainerTrait;
use Netzmacht\Workflow\Flow\Condition\Transition\TransitionPermissionCondition;
use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Security\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PermissionSubscriber integrates default permission features.
 *
 * @package Netzmacht\Workflow\Contao\Definition\Database
 */
class PermissionSubscriber implements EventSubscriberInterface
{
    use ServiceContainerTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            GetWorkflowPermissionsEvent::NAME => 'getPermissions',
            CreateTransitionEvent::NAME       => 'addTransitionConditions',
        );
    }

    /**
     * Get workflow permissions.
     *
     * @param GetWorkflowPermissionsEvent $event The subscribed event.
     *
     * @return void
     */
    public function getPermissions(GetWorkflowPermissionsEvent $event)
    {
        $workflow    = $event->getWorkflowModel();
        $permissions = deserialize($workflow->permissions, true);

        foreach ($permissions as $config) {
            $event->addPermission($config['name'], 'workflow');
        }

        $event
            ->addPermission('contao-admin', 'contao')
            ->addPermission('contao-guest', 'contao');
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
        $workflow   = $transition->getWorkflow();

        /** @var User $user */
        $user  = $this->getService('workflow.security.user');

        $condition = new ContaoTransitionPermissionCondition($user);
        $condition->grantAccessByDefault(true);

        $transition->addPreCondition($condition);
    }
}
