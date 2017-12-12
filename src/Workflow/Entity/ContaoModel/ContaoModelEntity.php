<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel;

use Contao\Model;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\Entity;

/**
 * Class ContaoModelEntity
 *
 * @package Netzmacht\ContaoWorkflowBundle\Entity
 */
class ContaoModelEntity implements \IteratorAggregate, Entity
{
    /**
     * The decorated model.
     *
     * @var Model
     */
    private $model;

    /**
     * ContaoModelEntity constructor.
     *
     * @param Model $model The model.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function getProviderName(): string
    {
        return $this->model::getTable();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->model->{$this->model::getPk()};
    }

    /**
     * {@inheritDoc}
     */
    public function getProperty(string $propertyName)
    {
        return $this->model->$propertyName;
    }

    /**
     * {@inheritDoc}
     */
    public function setProperty(string $propertyName, $value): Entity
    {
        $this->model->$propertyName = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasProperty(string $propertyName): bool
    {
        return isset($this->model->$propertyName);
    }

    /**
     * {@inheritDoc}
     */
    public function setProperties(array $properties): Entity
    {
        foreach ($properties as $name => $value) {
            $this->setProperty($name, $value);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->model->row());
    }

    /**
     * Get the wrapped model.
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
