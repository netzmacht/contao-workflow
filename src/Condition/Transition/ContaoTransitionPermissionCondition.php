<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\TransitionPermissionCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Security\Permission;

/**
 * Transition permission which recognize the contao Admin role.
 *
 * @package Netzmacht\Contao\Workflow\Condition\Transition
 */
class ContaoTransitionPermissionCondition extends TransitionPermissionCondition
{
    /**
     * {@inheritdoc}
     */
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $permission = Permission::forWorkflowName($transition->getWorkflow()->getName(), 'contao-admin');

        if ($this->user->hasPermission($permission)) {
            return true;
        }

        return parent::match($transition, $item, $context);
    }
}
