<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Workflow;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Util\Comparison;

/**
 * Property condition compares an entity property with an expected value
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class PropertyCondition implements Condition
{
    /**
     * Name of the property.
     */
    private string $property;

    /**
     * Value to compare with.
     */
    private mixed $value;

    /**
     * Comparison operator.
     */
    private string $operator;

    /**
     * Property access manager.
     */
    private PropertyAccessManager $propertyAccessManager;

    /**
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param string                $property              Name of the property.
     * @param mixed                 $value                 Value to compare with.
     * @param string                $operator              Comparison operator.
     */
    public function __construct(
        PropertyAccessManager $propertyAccessManager,
        string $property,
        $value,
        string $operator = Comparison::EQUALS,
    ) {
        $this->property              = $property;
        $this->value                 = $value;
        $this->operator              = $operator;
        $this->propertyAccessManager = $propertyAccessManager;
    }

    /**
     * {@inheritDoc}
     */
    public function match(Workflow $workflow, EntityId $entityId, $entity): bool
    {
        if (! $this->propertyAccessManager->supports($entity)) {
            return false;
        }

        $value = $this->propertyAccessManager->provideAccess($entity)->get($this->property);

        return Comparison::compare($value, $this->value, $this->operator);
    }
}
