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

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Model;
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBagInterface;

/**
 * Class DcGeneralModelDecorator implements an Entity using the decorator pattern for dc general models.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
class DcGeneralModelDecorator extends AbstractEntity
{
    /**
     * The actual model.
     *
     * @var Model
     */
    private $model;

    /**
     * Construct.
     *
     * @param Model $model The actual model.
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
        return $this->model->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->model = clone $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->model->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty($propertyName)
    {
        return $this->model->getProperty($propertyName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertiesAsArray()
    {
        return $this->model->getPropertiesAsArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($metaName)
    {
        return $this->model->getMeta($metaName);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($entityId)
    {
        $this->model->setId($entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty($propertyName, $value)
    {
        $this->model->setProperty($propertyName, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setPropertiesAsArray($properties)
    {
        $this->model->setPropertiesAsArray($properties);
    }

    /**
     * {@inheritdoc}
     */
    public function setMeta($metaName, $value)
    {
        $this->model->setMeta($metaName, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function hasProperties()
    {
        return $this->model->hasProperties();
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderName()
    {
        return $this->model->getProviderName();
    }

    /**
     * {@inheritdoc}
     */
    public function readFromPropertyValueBag(PropertyValueBagInterface $valueBag)
    {
        $this->model->readFromPropertyValueBag($valueBag);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function writeToPropertyValueBag(PropertyValueBagInterface $valueBag)
    {
        $this->model->writeToPropertyValueBag($valueBag);

        return $this;
    }
}
