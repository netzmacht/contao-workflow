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
     * @var array
     */
    private $metaData = array();

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
        return new \ArrayIterator($this->getPropertiesAsArray());
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->model->{$this->model->getPk()};
    }

    /**
     * {@inheritdoc}.
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
    public function setId($modelId)
    {
        $primaryKey = $this->model->getPk();

        $this->model->$primaryKey = $modelId;
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty($propertyName, $value)
    {
        if ($this->getProperty($propertyName) !== $value) {
            $this->setMeta(static::IS_CHANGED, true);
            $this->model->$propertyName = $value;
        }
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
        return (bool)count($this->getPropertiesAsArray());
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderName()
    {
        return $this->model->getTable();
    }

    /**
     * Fetch meta information from model.
     *
     * @param string $strMetaName The meta information to retrieve.
     *
     * @return mixed The set meta information or null if undefined.
     */
    public function getMeta($strMetaName)
    {
        if (isset($this->metaData[$strMetaName])) {
            return $this->metaData[$strMetaName];
        }

        return null;
    }

    /**
     * Update meta information in the model.
     *
     * @param string $strMetaName The meta information name.
     *
     * @param mixed  $varValue    The meta information value to store.
     *
     * @return void
     */
    public function setMeta($strMetaName, $varValue)
    {
        $this->metaData[$strMetaName] = $varValue;
    }


    /**
     * {@inheritdoc}
     *
     * @throws DcGeneralInvalidArgumentException If property is invalid.
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->model->id = null;
    }
}
