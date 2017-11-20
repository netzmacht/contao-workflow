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

namespace Netzmacht\Contao\Workflow\Manager;

use Netzmacht\Contao\Workflow\Data\EntityFactory;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandlerFactory;
use Netzmacht\Workflow\Manager\WorkflowManager;

/**
 * Class Manager adds entity handling to the manager.
 *
 * @package Netzmacht\Contao\Workflow
 */
class ContaoWorkflowManager extends WorkflowManager implements Manager
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
     *
     * @throws UnsupportedEntity When model could not be converted to an entity.
     */
    public function createItem(EntityId $entityId, $model): Item
    {
        if (!$model instanceof Entity) {
            $model = $this->entityFactory->createEntity($entityId, $model);
        }

        return parent::createItem($entityId, $model);
    }

    /**
     * Get workflow by id.
     *
     * @param int $workflowId The workflow id.
     *
     * @return Workflow
     *
     * @throws WorkflowNotFound When no workflow is registered with the given id.
     */
    public function getWorkflowById(int $workflowId): Workflow
    {
        /** @var Workflow $workflow */
        foreach ($this->getWorkflows() as $workflow) {
            if ($workflow->getConfigValue('id') == $workflowId) {
                return $workflow;
            }
        }

        throw new WorkflowNotFound(
            sprintf('Workflow with ID "%s" not found.', $workflowId)
        );
    }
}
