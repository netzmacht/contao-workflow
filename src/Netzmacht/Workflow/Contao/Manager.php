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

namespace Netzmacht\Workflow\Contao;

use Netzmacht\Workflow\Contao\Data\EntityFactory;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Factory;
use Netzmacht\Workflow\Factory\TransitionHandlerFactory;
use Netzmacht\Workflow\Flow\Item;

/**
 * Class Manager adds entity handling to the manager.
 *
 * @package Netzmacht\Workflow\Contao
 */
class Manager extends \Netzmacht\Workflow\Manager
{
    /**
     * The entity factory.
     *
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * Construct.
     *
     * @param TransitionHandlerFactory $handlerFactory  The transition handler factory.
     * @param StateRepository          $stateRepository The state repository.
     * @param EntityFactory            $entityFactory   The entity factory.
     * @param array                    $workflows       A optional set of workflows.
     */
    public function __construct(
        TransitionHandlerFactory $handlerFactory,
        StateRepository $stateRepository,
        EntityFactory $entityFactory,
        $workflows = array()
    ) {
        parent::__construct($handlerFactory, $stateRepository, $workflows);

        $this->entityFactory = $entityFactory;
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
        $entity = $this->entityFactory->createEntity($model, $entityId->getProviderName());

        return parent::createItem($entityId, $entity);
    }
}
