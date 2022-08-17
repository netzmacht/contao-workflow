<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Workflow;

use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowType;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class TypeCondition helps to decide if workflow type matches.
 */
final class TypeCondition implements Condition
{
    /**
     * Expected workflow type.
     *
     * @var WorkflowType
     */
    private $type;

    /**
     * @param WorkflowType $type Expected workflow type.
     */
    public function __construct(WorkflowType $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function match(Workflow $workflow, EntityId $entityId, $entity): bool
    {
        return $this->type->match((string) $workflow->getConfigValue('type'));
    }
}
