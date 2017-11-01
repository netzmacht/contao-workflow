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

namespace Netzmacht\Contao\Workflow\Definition\Event;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateEntityEvent is dispatched when a new entity is created.
 *
 * @package Netzmacht\Contao\Workflow\Factory\Event
 */
class CreateEntityEvent extends Event
{
    const NAME = 'workflow.factory.create-entity';

    /**
     * The entity being created.
     *
     * @var Entity
     */
    private $entity;

    /**
     * The given data model.
     *
     * @var mixed
     */
    private $model;

    /**
     * Optional table name.
     *
     * @var string|null
     */
    private $providerName;

    /**
     * Construct.
     *
     * @param mixed       $model        The data model.
     * @param string|null $providerName The table name.
     */
    public function __construct($model, $providerName = null)
    {
        $this->model        = $model;
        $this->providerName = $providerName;
    }

    /**
     * Get the data model.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get the table name.
     *
     * @return null|string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Get the created entity.
     *
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set the created entity.
     *
     * @param Entity $entity The created entity.
     *
     * @return $this
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;

        return $this;
    }
}
