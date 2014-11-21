<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Entity;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBagInterface;
use ContaoCommunityAlliance\DcGeneral\Exception\DcGeneralInvalidArgumentException;
use Model;

/**
 * Class ContaoModelEntity implements an Entity for Contao models.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
class ContaoModelEntity implements ModelInterface
{
    /**
     * The Contao model.
     *
     * @var Model
     */
    private $model;

    /**
     * Meta informations.
     *
     * @var array
     */
    private $meta;

    /**
     * Construct.
     *
     * @param Model $model The Contao model.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->model->row());
    }

    /**
     * Copy this model, without the id.
     *
     * @return void
     */
    public function __clone()
    {
        $this->model = clone $this->model;
    }

    /**
     * Get the Contao model.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty($propertyName)
    {
        return $this->model->$propertyName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertiesAsArray()
    {
        return $this->model->row();
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty($propertyName, $value)
    {
        if ($this->getProperty($propertyName) !== $value) {
            $this->setMeta(static::IS_CHANGED, true);
        }

        $this->model->$propertyName = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setPropertiesAsArray($properties)
    {
        foreach ($properties as $name => $value) {
            $this->setProperty($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasProperties()
    {
        return (bool) count($this->model->row());
    }

    /**
     * Get the id for this model.
     *
     * @return mixed The Id for this model.
     */
    public function getId()
    {
        $primaryKey = $this->model->getPk();

        return $this->model->$primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($metaName)
    {
        if (isset($this->meta[$metaName])) {
            return $this->meta[$metaName];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($modelId)
    {
        $primaryKey = $this->model->getPk();

        $this->model->$primaryKey = $modelId;
    }

    /**
     * {@inheritdoc}
     */
    public function setMeta($metaName, $value)
    {
        $this->meta[$metaName] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderName()
    {
        return $this->model->getTable();
    }

    /**
     * {@inheritdoc}
     *
     * @throws DcGeneralInvalidArgumentException If a property is marked as invalid.
     */
    public function readFromPropertyValueBag(PropertyValueBagInterface $valueBag)
    {
        $properties = array_keys($this->model->row());

        foreach ($properties as $name) {
            if (!$valueBag->hasPropertyValue($name)) {
                continue;
            }

            if ($valueBag->isPropertyValueInvalid($name)) {
                throw new DcGeneralInvalidArgumentException('The value for property ' . $name . ' is invalid.');
            }

            $this->setProperty($name, $valueBag->getPropertyValue($name));
        }

        return $this;
    }

    /**
     * Write values to a value bag.
     *
     * @param PropertyValueBagInterface $valueBag The value bag where to write to.
     *
     * @return ModelInterface
     */
    public function writeToPropertyValueBag(PropertyValueBagInterface $valueBag)
    {
        $properties = array_keys($this->model->row());

        foreach ($properties as $name) {
            if (!$valueBag->hasPropertyValue($name)) {
                continue;
            }

            $valueBag->setPropertyValue($name, $this->getProperty($name));
        }

        return $this;
    }
}
