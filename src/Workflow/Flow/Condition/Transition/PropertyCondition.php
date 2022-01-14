<?php

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
 * @SuppressWarnings(PHPMD.LongVariable)
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
     * @var string|null
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
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     */
    public function __construct(PropertyAccessManager $propertyAccessManager)
    {
        $this->propertyAccessManager = $propertyAccessManager;
    }

    /**
     * Get comparison operator.
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

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $entity = $item->getEntity();

        if (! $this->property) {
            $context->addError('transition.condition.property.invalid_property');

            return false;
        }

        if (! $this->propertyAccessManager->supports($entity)) {
            $context->addError('transition.condition.property.invalid_entity');

            return false;
        }

        $value = $this->propertyAccessManager->provideAccess($entity)->get($this->property);

        if (Comparison::compare($value, $this->value, $this->operator)) {
            return true;
        }

        $context->addError(
            'transition.condition.property.failed',
            [
                '%property%'       => $this->property,
                '%value%'          => $value,
                '%operator%'       => $this->operator,
                '%expected_value%' => $this->value,
            ]
        );

        return false;
    }
}
