<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Transition\Condition;

use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\ErrorCollection;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition\Condition;
use Netzmacht\Contao\Workflow\Util\Comparison;

class EntityPropertyCondition implements Condition
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param Entity          $entity
     * @param Context         $context
     * @param ErrorCollection $errorCollection
     *
     * @return bool
     */
    public function match(Entity $entity, Context $context, ErrorCollection $errorCollection)
    {
        return Comparison::compare(
            $entity->getProperty($this->property),
            $this->value,
            $this->operator
        );
    }
}