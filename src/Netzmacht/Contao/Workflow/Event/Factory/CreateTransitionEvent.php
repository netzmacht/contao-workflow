<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Event\Factory;

use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\EventDispatcher\Event;

class CreateTransitionEvent extends Event
{
    const NAME = 'workflow.factory.create-transition';

    /**
     * @var \Netzmacht\Workflow\Flow\Transition
     */
    private $transition;

    /**
     * Construct.
     *
     * @param \Netzmacht\Workflow\Flow\Transition $transition
     */
    function __construct(Transition $transition)
    {
        $this->transition = $transition;
    }

    /**
     * @return \Netzmacht\Workflow\Flow\Transition
     */
    public function getTransition()
    {
        return $this->transition;
    }
}
