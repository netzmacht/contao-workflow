<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

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
     * TypeCondition constructor.
     *
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
