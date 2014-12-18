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

namespace Netzmacht\Workflow\Contao\Data;

use ContaoCommunityAlliance\DcGeneral\Data\DefaultModel;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Database\Result;
use Netzmacht\Workflow\Contao\Data\ContaoModelEntity;
use Netzmacht\Workflow\Contao\Definition\Event;
use Netzmacht\Workflow\Contao\Definition\Event\CreateEntityEvent;
 use MetaModels\IItem as MetaModelItem;
use Model\Collection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EntityFactory is responsible to createRepository the Entity for different model types.
 *
 * It acts as an event subscriber but can be used as standalone factory as well.
 *
 * @package Netzmacht\Contao\Workflow\Factory
 */
class EntityFactory implements EventSubscriberInterface
{
    /**
     * Get subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateEntityEvent::NAME => 'handleCreateEntityEvent'
        );
    }

    /**
     * Handle the createRepository entity event.
     *
     * @param CreateEntityEvent $event Event subscribed to.
     *
     * @return void
     */
    public function handleCreateEntityEvent(CreateEntityEvent $event)
    {
        $model  = $event->getModel();
        $entity = $this->createEntity($model, $event->getProviderName());

        $event->setEntity($entity);
    }

    /**
     * Create a new entity.
     *
     * @param mixed  $model     The data model.
     * @param string $tableName The table name.
     *
     * @throws \InvalidArgumentException If unsupported entity format is given.
     *
     * @return Entity
     */
    public function createEntity($model, $tableName = null)
    {
        if ($model instanceof Entity) {
            return $model;
        }

        if ($model instanceof \Model) {
            $entity = $this->createFromContaoModel($model);
        } elseif ($model instanceof Collection) {
            $entity = $this->createFromContaoModel($model->current());
        } elseif ($model instanceof MetaModelItem) {
            $entity = $this->createFromMetaModel($model);
        } else {
            \Assert\that($tableName)
                ->notBlank('Table attribute is required')
                ->string('Table attribute has to be a string');

            if ($model instanceof Result) {
                $model = $model->row();
            } elseif (!is_array($model)) {
                throw new \InvalidArgumentException('Unsupported model format.');
            }

            $entity = $this->createFromArray($model, $tableName);
        }

        return $entity;
    }

    /**
     * Create an entity from a Contao model.
     *
     * @param \Model $model Contao model.
     *
     * @return Entity
     */
    public function createFromContaoModel(\Model $model)
    {
        return new ContaoModelEntity($model);
    }


    /**
     * Create an entity from a metamodel.
     *
     * @param MetaModelItem $item MetaModel.
     *
     * @return Entity
     */
    public function createFromMetaModel(MetaModelItem $item)
    {
        return new MetaModelModel($item);
    }

    /**
     * Create an entity based on a database result.
     *
     * @param Result $result    The database result.
     * @param string $tableName The table name.
     *
     * @return Entity
     */
    public function createFromResult(Result $result, $tableName)
    {
        return $this->createFromArray($result->row(), $tableName);
    }

    /**
     * Create an entity from a data row.
     *
     * @param array  $row       The data row.
     * @param string $tableName The table name.
     *
     * @return Entity
     */
    public function createFromArray(array $row, $tableName)
    {
        $model = new DefaultModel();
        $model->setProviderName($tableName);

        foreach ($row as $name => $value) {
            $model->setPropertyRaw($name, $value);
        }

        return $model;
    }
}
