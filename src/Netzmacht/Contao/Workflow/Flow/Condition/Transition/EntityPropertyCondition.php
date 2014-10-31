<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Condition\Transition;

use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Item;
use Netzmacht\Contao\Workflow\Util\Comparison;

/**
 * Class EntityPropertyCondition allows to define condition based on the entity properties.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Transition\Condition
 */
class EntityPropertyCondition implements Condition
{
    /**
     * The property name.
     *
     * @var string
     */
    private $property;

    /**
     * The comparison operator.
     *
     * @var string
     */
    private $operator;

    /**
     * The value to compare with.
     *
     * @var mixed
     */
    private $value;

    /**
     * Consider if property condition matches.
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return bool
     */
    public function match(Transition $transition, Item $item, Context $context)
    {
        return Comparison::compare(
            $item->getEntity()->getProperty($this->property),
            $this->value,
            $this->operator
        );
    }
}
