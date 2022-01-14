<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event;

use Netzmacht\Workflow\Flow\Transition;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class CreateTransitionEvent is emitted when transition is created.
 */
final class CreateTransitionEvent extends Event
{
    public const NAME = 'netzmacht.contao_workflow.create_transition';

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
     */
    public function getTransition(): Transition
    {
        return $this->transition;
    }
}
