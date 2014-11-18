<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Flow\Event;

use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\EventDispatcher\Event;

class CreateTransitionEvent extends Event
{
    const NAME = 'workflow.factory.create-transition';

    /**
     * @var Transition
     */
    private $transition;

    /**
     * Construct.
     *
     * @param Transition $transition
     */
    function __construct(Transition $transition)
    {
        $this->transition = $transition;
    }

    /**
     * @return Transition
     */
    public function getTransition()
    {
        return $this->transition;
    }
}
