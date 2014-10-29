<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Factory;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use Database\Result;
use MetaModels\DcGeneral\Data\Model;
use Model\Collection;
use Netzmacht\Contao\Workflow\Entity\ArrayDecorator;
use Netzmacht\Contao\Workflow\Entity\ContaoModelEntity;
use Netzmacht\Contao\Workflow\Entity\DcGeneralModelDecorator;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Factory\Event\CreateEntityEvent;
use MetaModels\IItem as MetaModelItem;
use Netzmacht\Contao\Workflow\Model\StateRepository;
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
     * @return Entity
     */
    public function handleCreateEntityEvent(CreateEntityEvent $event)
    {
        $model  = $event->getModel();
        $entity = $this->createEntity($model, $event->getTable());

        if ($entity) {
            $state = $this->createState($entity);

            if ($state) {
                $entity->transit($state);
            }

            $event->setEntity($entity);
        }
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
        if ($model instanceof ModelInterface) {
            $entity = $this->createFromDcGeneralModel($model);
        } elseif ($model instanceof \Model) {
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
     * Create an entity from a dc general model.
     *
     * @param ModelInterface $model DcGeneral model.
     *
     * @return Entity
     */
    public function createFromDcGeneralModel(ModelInterface $model)
    {
        return new DcGeneralModelDecorator($model);
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
        $wrapper = new Model($item);

        return $this->createFromDcGeneralModel($wrapper);
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
        return new ArrayDecorator($row, $tableName);
    }

    /**
     * @param $entity
     *
     * @return \Netzmacht\Contao\Workflow\Model\State
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function createState(Entity $entity)
    {
        /** @var StateRepository $repository */
        $repository = $GLOBALS['container']['workflow.state-repository'];

        return $repository->find($entity->getProviderName(), $entity->getId());
    }
}
