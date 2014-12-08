<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Action;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class PropertyAction changes the value of an entity property.
 *
 * @package Netzmacht\Workflow\Contao\Action
 */
class PropertyAction extends AbstractAction
{
    /**
     * Name of the property.
     *
     * @var string
     */
    private $property;

    /**
     * Value to be set.
     *
     * @var mixed
     */
    private $value;

    /**
     * {@inheritdoc}
     */
    public function transit(Transition $transition, Item $item, Context $context)
    {
        $entity = $this->getEntity($item);
        $value  = $this->getValueForProperty($item, $context);

        $entity->setProperty($this->property, $value);
        $this->logChanges($this->property, $value, $context);
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set the property name which should be updated.
     *
     * @param string $property The property name.
     *
     * @return $this
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get the property value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value which will be set.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value for the property from the context.
     *
     * If value is given, it is used. If input is required then value from the context is extracted.
     *
     * @param Item    $item    The workflow item.
     * @param Context $context The transition contaext.
     *
     * @return mixed
     */
    private function getValueForProperty(Item $item, Context $context)
    {
        if ($this->value !== null) {
            return $this->value;
        } elseif ($this->isInputRequired($item)) {
            return $context->getParam($this->property);
        }

        return null;
    }
}
