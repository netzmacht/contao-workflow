<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Definition\Event;

use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateTransitionEvent is emitted when transition is created.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Definition\Event
 */
class CreateTransitionEvent extends Event
{
    const NAME = 'netzmacht.contao_workflow.create_transition';

    /**
     * The created transition.
     *
     * @var Transition
     */
    private $transition;

    /**
     * Construct.
     *
     * @param Transition $transition Workflow transition.
     */
    public function __construct(Transition $transition)
    {
        $this->transition = $transition;
    }

    /**
     * Get the transition.
     *
     * @return Transition
     */
    public function getTransition(): Transition
    {
        return $this->transition;
    }
}
