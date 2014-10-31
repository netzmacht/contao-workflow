<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Action;


use Netzmacht\Contao\Workflow\Action\Event\ExecuteTransitionEvent;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Item;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class EventDispatcherAction just dispatches an event that transition action is executed.
 *
 * So it's easy to plugin some action handler which should be called on every action. It's recommend listen to the
 * event instead of assigning an action to each transition.
 *
 * @package Netzmacht\Contao\Workflow\Action
 */
class EventDispatcherAction extends AbstractAction
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     */
    function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function transit(Transition $transition, Item $item, Context $context)
    {
        $event = new ExecuteTransitionEvent($transition, $item, $context);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }
}
