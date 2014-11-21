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
        $entity->setProperty($this->property, $this->getValue($context));
    }

    /**
     * Get value for the property.
     *
     * If value is given, it is used. If input is required then value from the context is extracted.
     *
     * @param Context $context The transition contaext.
     *
     * @return mixed
     */
    private function getValue(Context $context)
    {
        if ($this->value !== null) {
            return $this->value;
        } elseif ($this->requiresInputData()) {
            return $context->getParam($this->property);
        }

        return null;
    }
}
