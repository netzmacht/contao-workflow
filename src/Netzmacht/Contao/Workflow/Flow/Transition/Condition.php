<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Transition;

use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;

/**
 * Interface Condition describes an condition which used for transtion conditions.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Transition
 */
interface Condition
{
    /**
     * Consider if condition matches for the given entity.
     *
     * @param Transition $transition The transition being in.
     * @param Entity     $entity     The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return bool
     */
    public function match(Transition $transition, Entity $entity, Context $context);
}
