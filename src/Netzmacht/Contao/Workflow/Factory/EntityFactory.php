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
use Netzmacht\Contao\Workflow\Entity\ContaoModelEntity;
use Netzmacht\Contao\Workflow\Entity\DcGeneralModelDecorator;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Factory\Event\CreateEntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntityFactory implements EventSubscriberInterface
{
    /**
     * @{inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateEntityEvent::NAME => 'createEntity'
        );
    }

    /**
     * @param CreateEntityEvent $event
     */
    public function createEntity(CreateEntityEvent $event)
    {
        $model = $event->getModel();

        if ($model instanceof ModelInterface) {
            $entity = $this->createFromDcGeneralModel($model);
        } elseif ($model instanceof \Model) {
            $entity = $this->createFromContaoModel($model);
        }
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
}
