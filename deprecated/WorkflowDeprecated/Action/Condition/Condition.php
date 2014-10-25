<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Action\Condition;


use Netzmacht\Contao\WorkflowDeprecated\Action;
use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Netzmacht\Contao\WorkflowDeprecated\Flow\Transition;

interface Condition
{
    /**
     * @param Action $action
     * @param Transition $transition
     * @param Entity $entity
     * @return bool
     */
    public function match(Action $action, Transition $transition, Entity $entity);

} 