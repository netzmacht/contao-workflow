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


use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Item;
use Netzmacht\Contao\Workflow\Model\State;
use Symfony\Component\EventDispatcher\Event;

class SepReachedEvent extends Event
{
    const NAME = 'workflow.transition.handler.step-reached';

    /**
     * @var State
     */
    private $state;

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var Item
     */
    private $item;

    /**
     * @param Workflow $workflow
     * @param Item     $item
     * @param State    $state
     */
    function __construct(Workflow $workflow, Item $item, State $state)
    {
        $this->state = $state;
    }

    /**
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }
}
