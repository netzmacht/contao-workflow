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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Util\Comparison;

/**
 * Class PropertyCondition compares an entity property against a defined value.
 *
 * @package Netzmacht\Workflow\Flow\Condition\Transition
 */
final class PropertyCondition implements Condition
{
    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * Name of the property.
     *
     * @var string
     */
    private $property;

    /**
     * Comparison operator.
     *
     * @var string
     */
    private $operator = Comparison::EQUALS;

    /**
     * Value to compare with.
     *
     * @var mixed
     */
    private $value;

    /**
     * PropertyCondition constructor.
     *
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     */
    public function __construct(PropertyAccessManager $propertyAccessManager)
    {
        $this->propertyAccessManager = $propertyAccessManager;
    }

    /**
     * Get comparison operator.
     *
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Set property name.
     *
     * @param string $operator Comparison operator name.
     *
     * @return $this
     */
    public function setOperator(string $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get Property name.
     *
     * @return string
     */
    public function getProperty(): ?string
    {
        return $this->property;
    }

    /**
     * Set comparison property.
     *
     * @param string $property Comparison property.
     *
     * @return $this
     */
    public function setProperty(string $property): self
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get value to compare against.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set Value to compare against.
     *
     * @param mixed $value The comparison value.
     *
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $entity = $item->getEntity();

        if (!$this->propertyAccessManager->supports($entity)) {
            $context->addError('transition.condition.property.invalid_entity');

            return false;
        }

        $value = $this->propertyAccessManager->provideAccess($entity)->get($this->property);

        if (Comparison::compare($value, $this->value, $this->operator)) {
            return true;
        }

        $context->addError(
            'transition.condition.property.failed',
            array(
                '%property%'       => $this->property,
                '%value%'          => $value,
                '%operator%'       => $this->operator,
                '%expected_value%' => $this->value,
            )
        );

        return false;
    }
}
