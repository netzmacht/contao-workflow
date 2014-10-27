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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntityFactory implements EventSubscriberInterface
{
    /**
     * @{inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateEntityEvent::NAME => 'handleCreateEntityEvent'
        );
    }

    /**
     * @param CreateEntityEvent $event
     *
     * @return Entity
     */
    public function handleCreateEntityEvent(CreateEntityEvent $event)
    {
        $model = $event->getModel();

        return $this->createEntity($model);
    }

    /**
     * @param        $model
     * @param string $tableName
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
     * @param \Model $model
     *
     * @return Entity
     */
    public function createFromContaoModel(\Model $model)
    {
        return new ContaoModelEntity($model);
    }

    /**
     * @param ModelInterface $model
     *
     * @return Entity
     */
    public function createFromDcGeneralModel(ModelInterface $model)
    {
        return new DcGeneralModelDecorator($model);
    }

    /**
     * @param MetaModelItem $item
     *
     * @return Entity
     */
    public function createFromMetaModel(MetaModelItem $item)
    {
        $wrapper = new Model($item);

        return $this->createFromDcGeneralModel($wrapper);
    }

    /**
     * @param Result $result
     * @param        $tableName
     *
     * @return ArrayDecorator
     */
    public function createFromResult(Result $result, $tableName)
    {
        return $this->createFromArray($result->row(), $tableName);
    }

    /**
     * @param array $row
     * @param       $tableName
     *
     * @return ArrayDecorator
     */
    public function createFromArray(array $row, $tableName)
    {
        return new ArrayDecorator($row, $tableName);
    }
}
