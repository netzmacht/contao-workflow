<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Entity;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBagInterface;
use ContaoCommunityAlliance\DcGeneral\Exception\DcGeneralInvalidArgumentException;
use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Traversable;

class DcGeneralModelDecorator extends AbstractEntity
{
    /**
     * @var ModelInterface
     */
    private $model;

    /**
     * @param ModelInterface $model
     */
    function __construct(ModelInterface $model)
    {
        $this->model = $model;
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
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->model->getIterator();
    }

    /**
     * Get the id for this model.
     *
     * @return mixed The Id for this model.
     */
    public function getId()
    {
        return $this->model->getId();
    }

    /**
     * Fetch the property with the given name from the model.
     *
     * This method returns null if an unknown property is retrieved.
     *
     * @param string $strPropertyName The property name to be retrieved.
     *
     * @return mixed The value of the given property.
     */
    public function getProperty($strPropertyName)
    {
        return $this->model->getProperty($strPropertyName);
    }

    /**
     * Fetch all properties from the model as an name => value array.
     *
     * @return array
     */
    public function getPropertiesAsArray()
    {
        return $this->model->getPropertiesAsArray();
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
        return $this->model->getMeta($strMetaName);
    }

    /**
     * Set the id for this object.
     *
     * NOTE: when the Id has been set once to a non null value, it can NOT be changed anymore.
     *
     * Normally this should only be called from inside of the implementing provider.
     *
     * @param mixed $mixId Could be a integer, string or anything else - depends on the provider implementation.
     *
     * @return void
     */
    public function setId($mixId)
    {
        $this->model->setId($mixId);
    }

    /**
     * Update the property value in the model.
     *
     * @param string $strPropertyName The property name to be set.
     *
     * @param mixed $varValue The value to be set.
     *
     * @return void
     */
    public function setProperty($strPropertyName, $varValue)
    {
        $this->model->setProperty($strPropertyName, $varValue);
    }

    /**
     * Update all properties in the model.
     *
     * @param array $arrProperties The property values as name => value pairs.
     *
     * @return void
     */
    public function setPropertiesAsArray($arrProperties)
    {
        $this->model->setPropertiesAsArray($arrProperties);
    }

    /**
     * Update meta information in the model.
     *
     * @param string $strMetaName The meta information name.
     *
     * @param mixed $varValue The meta information value to store.
     *
     * @return void
     */
    public function setMeta($strMetaName, $varValue)
    {
        $this->model->setMeta($strMetaName, $varValue);
    }

    /**
     * Check if this model have any properties.
     *
     * @return boolean true if any property has been stored, false otherwise.
     */
    public function hasProperties()
    {
        return $this->model->hasProperties();
    }

    /**
     * Return the data provider name.
     *
     * @return string the name of the corresponding data provider.
     */
    public function getProviderName()
    {
        return $this->model->getProviderName();
    }

    /**
     * Read all values from a value bag.
     *
     * If the value is not present in the value bag, it will get skipped.
     *
     * If the value for a property in the bag is invalid, an exception will get thrown.
     *
     * @param PropertyValueBagInterface $valueBag The value bag where to read from.
     *
     * @return ModelInterface
     *
     * @throws DcGeneralInvalidArgumentException When a property in the value bag has been marked as invalid.
     */
    public function readFromPropertyValueBag(PropertyValueBagInterface $valueBag)
    {
        $this->model->readFromPropertyValueBag($valueBag);

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
        $this->model->writeToPropertyValueBag($valueBag);

        return $this;
    }

} 