<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Transition\Condition;


use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Transition\Condition;

class PermissionCondition implements Condition
{
    /**
     * @var \BackendUser
     */
    private $user;

    /**
     * @param $user
     */
    function __construct(\BackendUser $user)
    {
        $this->user = $user;
    }

    /**
     * @param Transition $transition
     * @param Entity     $entity
     * @param Context    $context
     *
     * @return bool
     */
    public function match(Transition $transition, Entity $entity, Context $context)
    {
        if (array_intersect($transition->getRoles(), $this->user->groups)) {
            return true;
        }

        return false;
    }
}
