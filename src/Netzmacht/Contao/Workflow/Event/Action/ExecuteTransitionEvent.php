<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Event\Action;


use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Item;
use Symfony\Component\EventDispatcher\Event;

class ExecuteTransitionEvent extends Event
{
    const NAME = 'workflow.transition.execute-action';

    /**
     * The executed transition.
     *
     * @var \Netzmacht\Workflow\Flow\Transition
     */
    private $transition;

    /**
     * The transition context.
     *
     * @var \Netzmacht\Workflow\Flow\Context
     */
    private $context;

    /**
     * The current item.
     *
     * @var Item
     */
    private $item;

    /**
     * @param \Netzmacht\Workflow\Flow\Transition $transition The executed transition.
     * @param Item       $item       The current item.
     * @param \Netzmacht\Workflow\Flow\Context    $context    The transition context.
     */
    public function __construct(Transition $transition, Item $item, Context $context)
    {
        $this->transition = $transition;
        $this->item       = $item;
        $this->context    = $context;
    }

    /**
     * Get the context the transition is in.
     *
     * @return \Netzmacht\Workflow\Flow\Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get the item which is transited.
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Get the executed transition.
     *
     * @return \Netzmacht\Workflow\Flow\Transition
     */
    public function getTransition()
    {
        return $this->transition;
    }
}
