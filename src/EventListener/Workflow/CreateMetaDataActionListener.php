<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Workflow;

use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Definition;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateTransitionEvent;
use Netzmacht\Workflow\Flow\Action;

/**
 * Class CreateMetaDataActionListener adds the MetaDataAction to every database driven workflow transition.
 */
final class CreateMetaDataActionListener
{
    /**
     * The action.
     *
     * @var Action
     */
    private $metadataAction;

    /**
     * @param Action $action The metadata action.
     */
    public function __construct(Action $action)
    {
        $this->metadataAction = $action;
    }

    /**
     * Handle the event.
     *
     * @param CreateTransitionEvent $event The event.
     */
    public function onCreateTransition(CreateTransitionEvent $event): void
    {
        $transition = $event->getTransition();

        if ($transition->getConfigValue(Definition::SOURCE) !== Definition::SOURCE_DATABASE) {
            return;
        }

        $event->getTransition()->addAction($this->metadataAction);
    }
}
