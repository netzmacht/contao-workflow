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

/**
 * Class Factory for creating workflow manager and entities.
 *
 * @package Netzmacht\Contao\Workflow
 */
class Factory
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
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Create a manager for a specific type.
     *
     * @param string $type Workflow type.
     *
     * @throws \RuntimeException If no manager was created.
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
     * Create a new entity for a model.
     *
     * @param mixed       $model Create an workflow entity.
     * @param string|null $table Table name is required for Contao results or array rows.
     *
     * @throws \RuntimeException If no entity could be created.
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
