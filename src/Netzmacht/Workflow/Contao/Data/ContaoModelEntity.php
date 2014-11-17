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
    public function setProperty($strPropertyName, $varValue)
    {
        $this->model->$strPropertyName = $varValue;
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
     * Fetch meta information from model.
     *
     * @param string $strMetaName The meta information to retrieve.
     *
     * @return mixed The set meta information or null if undefined.
     */
    public function getMeta($strMetaName)
    {
        if (isset($this->meta[$strMetaName])) {

        }
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
        // TODO: Implement setId() method.
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
        // TODO: Implement setMeta() method.
    }

    /**
     * Return the data provider name.
     *
     * @return string the name of the corresponding data provider.
     */
    public function getProviderName()
    {
        // TODO: Implement getProviderName() method.
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
        // TODO: Implement readFromPropertyValueBag() method.
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
        // TODO: Implement writeToPropertyValueBag() method.
    }
}
