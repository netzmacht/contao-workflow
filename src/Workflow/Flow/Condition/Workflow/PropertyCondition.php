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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Workflow;

use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\EntityWithPropertyAccess;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Util\Comparison;

/**
 * Property condition compares an entity property with an expected value
 */
final class PropertyCondition implements Condition
{
    /**
     * Name of the property.
     *
     * @var string
     */
    private $property;

    /**
     * Value to compare with.
     *
     * @var mixed
     */
    private $value;

    /**
     * Comparison operator.
     *
     * @var string
     */
    private $operator;

    /**
     * PropertyCondition constructor.
     *
     * @param string $property Name of the property.
     * @param mixed  $value    Value to compare with.
     * @param string $operator Comparison operator.
     */
    public function __construct(string $property, $value, $operator = Comparison::EQUALS)
    {
        $this->property = $property;
        $this->value    = $value;
        $this->operator = $operator;
    }

    /**
     * {@inheritdoc}
     */
    public function match(Workflow $workflow, EntityId $entityId, $entity): bool
    {
        if (!$entity instanceof EntityWithPropertyAccess) {
            return false;
        }

        $value = $entity->getProperty($this->property);

        return Comparison::compare($value, $this->value, $this->operator);
    }
}
