<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow;


use Netzmacht\Contao\Workflow\Factory\Event\CreateEntityEvent;
use Netzmacht\Contao\Workflow\Factory\Event\CreateManagerEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

class Factory
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $type
     *
     * @return Manager
     */
    public function createManager($type)
    {
        $event = new CreateManagerEvent($type);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        $manager = $event->getManager();
        if (!$manager) {
            throw new \RuntimeException(
                sprintf('No workflow manager were created during dispatching event "%s"', $event::NAME)
            );
        }

        return $manager;
    }

    /**
     * @param      $model
     * @param null $table
     *
     * @return Entity\Entity
     */
    public function createEntity($model, $table = null)
    {
        $event = new CreateEntityEvent($model, $table);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        $entity = $event->getEntity();
        if (!$entity) {
            throw new \RuntimeException(
                sprintf('No entity were created during dispatching event "%s"', $event::NAME)
            );
        }

        return $entity;
    }
}
