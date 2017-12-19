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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Workflow;

use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Definition;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateTransitionEvent;
use Netzmacht\Workflow\Flow\Action;

/**
 * Class CreateMetaDataActionListener adds the MetaDataAction to every database driven workflow transition.
 */
class CreateMetaDataActionListener
{
    /**
     * The action.
     *
     * @var Action
     */
    private $metadataAction;

    /**
     * CreateMetaDataActionListener constructor.
     *
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
     *
     * @return void
     */
    public function onCreateTransition(CreateTransitionEvent $event): void
    {
        $transition = $event->getTransition();

        if ($transition->getConfigValue(Definition::SOURCE) === Definition::SOURCE_DATABASE) {
            $event->getTransition()->addAction($this->metadataAction);
        }
    }
}
