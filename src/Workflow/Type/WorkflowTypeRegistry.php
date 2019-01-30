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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Type;

/**
 * Class WorkflowTypeProvider.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Type
 */
final class WorkflowTypeRegistry
{
    /**
     * Workflow types.
     *
     * @var WorkflowType[]|array
     */
    private $types;

    /**
     * WorkflowTypeProvider constructor.
     *
     * @param array|WorkflowType[] $types Workflow types.
     */
    public function __construct(iterable $types)
    {
        $this->types = $types;
    }

    /**
     * Get all workflow type names.
     *
     * @return string[]
     */
    public function getTypeNames(): array
    {
        $names = [];

        foreach ($this->types as $workflowType) {
            $names[] = $workflowType->getName();
        }

        return $names;
    }

    /**
     * Get a workflow type by name.
     *
     * @param string $typeName Name of the workflow type.
     *
     * @return WorkflowType
     *
     * @throws WorkflowTypeNotFound When workflow type is not registered.
     */
    public function getType(string $typeName): WorkflowType
    {
        foreach ($this->types as $workflowType) {
            if ($workflowType->getName() === $typeName) {
                return $workflowType;
            }
        }

        throw WorkflowTypeNotFound::withName($typeName);
    }

    /**
     * Check if a type is know.
     *
     * @param string $typeName Workflow type name.
     *
     * @return bool
     */
    public function hasType(string $typeName): bool
    {
        foreach ($this->types as $workflowType) {
            if ($workflowType->getName() === $typeName) {
                return true;
            }
        }

        return false;
    }
}
