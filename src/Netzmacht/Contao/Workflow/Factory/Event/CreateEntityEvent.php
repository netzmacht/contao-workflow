<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Factory\Event;


use Netzmacht\Contao\Workflow\Entity\Entity;
use Symfony\Component\EventDispatcher\Event;

class CreateEntityEvent extends Event
{
    const NAME = 'workflow.factory.create-entity';

    /**
     * @var Entity
     */
    private $entity;

    /**
     * @var mixed
     */
    private $model;

    /**
     * @var string|null
     */
    private $table;

    /**
     * @param      $model
     * @param null $table
     */
    public function __construct($model, $table = null)
    {
        $this->model = $model;
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return null|string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;

        return $this;
    }
}
