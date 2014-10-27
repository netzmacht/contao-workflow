<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Entity;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBagInterface;
use ContaoCommunityAlliance\DcGeneral\Exception\DcGeneralInvalidArgumentException;
use Model;

class ContaoModelEntity extends AbstractEntity
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @param $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @{inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getPropertiesAsArray());
    }

    /**
     * @{inheritdoc}
     */
    public function getId()
    {
        return $this->model->{$this->model->getPk()};
    }

    /**
     * Fetch the property with the given name from the model.
     *
     * This method returns null if an unknown property is retrieved.
     *
     * @param string $propertyName The property name to be retrieved.
     *
     * @return mixed The value of the given property.
     */
    public function getProperty($propertyName)
    {
        return $this->model->$propertyName;
    }

    /**
     * Fetch all properties from the model as an name => value array.
     *
     * @return array
     */
    public function getPropertiesAsArray()
    {
        return $this->model->row();
    }

    /**
     * Set the id for this object.
     *
     * NOTE: when the Id has been set once to a non null value, it can NOT be changed anymore.
     *
     * Normally this should only be called from inside of the implementing provider.
     *
     * @param mixed $modelId Could be a integer, string or anything else - depends on the provider implementation.
     *
     * @return void
     */
    public function setId($modelId)
    {
        $primaryKey = $this->model->getPk();
        $this->model->$primaryKey = $modelId;
    }

    /**
     * Update the property value in the model.
     *
     * @param string $propertyName The property name to be set.
     *
     * @param mixed $value The value to be set.
     *
     * @return void
     */
    public function setProperty($propertyName, $value)
    {
        if ($this->getProperty($propertyName) !== $value) {
            $this->setMeta(static::IS_CHANGED, true);
            $this->model->$propertyName = $value;
        }
    }

    /**
     * Update all properties in the model.
     *
     * @param array $properties The property values as name => value pairs.
     *
     * @return void
     */
    public function setPropertiesAsArray($properties)
    {
        foreach ($properties as $name => $value) {
            $this->setProperty($name, $value);
        }
    }

    /**
     * Check if this model have any properties.
     *
     * @return boolean true if any property has been stored, false otherwise.
     */
    public function hasProperties()
    {
        return (bool) count($this->getPropertiesAsArray());
    }

    /**
     * Return the data provider name.
     *
     * @return string the name of the corresponding data provider.
     */
    public function getProviderName()
    {
        return $this->model->getTable();
    }

    /**
     * @{inheritdoc}
     */
    public function readFromPropertyValueBag(PropertyValueBagInterface $valueBag)
    {
        foreach (array_keys($this->getPropertiesAsArray()) as $name) {
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
        foreach ($this->getPropertiesAsArray() as $name => $value) {
            if ($valueBag->hasPropertyValue($name)) {
                $valueBag->setPropertyValue($name, $value);
            }
        }

        return $this;
    }

    /**
     * Copy this model, without the id.
     *
     * @return void
     */
    public function __clone()
    {
        $this->model->id = null;
    }
}
