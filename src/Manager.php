<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow;

use Netzmacht\Contao\Workflow\Data\EntityFactory;
use Netzmacht\Contao\Workflow\Definition\Event\CreateEntityEvent;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Factory;
use Netzmacht\Workflow\Factory\TransitionHandlerFactory;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Manager\WorkflowManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class Manager adds entity handling to the manager.
 *
 * @package Netzmacht\Contao\Workflow
 */
class Manager extends WorkflowManager
{
    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Construct.
     *
     * @param TransitionHandlerFactory $handlerFactory  The transition handler factory.
     * @param StateRepository          $stateRepository The state repository.
     * @param EventDispatcher          $eventDispatcher The event dispatcher.
     * @param array                    $workflows       A optional set of workflows.
     */
    public function __construct(
        TransitionHandlerFactory $handlerFactory,
        StateRepository $stateRepository,
        EventDispatcher $eventDispatcher,
        $workflows = array()
    ) {
        parent::__construct($handlerFactory, $stateRepository, $workflows);

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Create the item.
     *
     * It also converts the entity to a ModelInterface instance.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $model    The data model.
     *
     * @return Item
     */
    public function createItem(EntityId $entityId, $model)
    {
        $event = new CreateEntityEvent($model, $entityId->getProviderName());
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return parent::createItem($entityId, $event->getEntity());
    }
}
