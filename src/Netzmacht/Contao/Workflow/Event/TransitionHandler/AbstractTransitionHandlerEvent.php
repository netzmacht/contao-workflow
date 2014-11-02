<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Event\TransitionHandler;


use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Item;
use Symfony\Component\EventDispatcher\Event;

class AbstractTransitionHandlerEvent extends Event
{

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var Transition
     */
    private $transition;

    /**
     * @var Item
     */
    private $item;

    /**
     * @param $workflow
     * @param $transition
     * @param $item
     */
    function __construct(Workflow $workflow, Transition $transition, Item $item)
    {
        $this->workflow   = $workflow;
        $this->transition = $transition;
        $this->item       = $item;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return Transition
     */
    public function getTransition()
    {
        return $this->transition;
    }

    /**
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }
}
