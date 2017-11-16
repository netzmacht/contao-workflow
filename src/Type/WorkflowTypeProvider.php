<?php

/**
 * @package    Elephant City
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Type;

/**
 * Class WorkflowTypeProvider.
 *
 * @package Netzmacht\Contao\Workflow\Type
 */
class WorkflowTypeProvider
{
    /**
     * @var WorkflowType[]|array
     */
    private $types;

    /**
     * WorkflowTypeProvider constructor.
     *
     * @param array|WorkflowType[] $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * Get all workflow type names.
     *
     * @return array
     */
    public function getTypeNames()
    {
        return array_map(
            function (WorkflowType $workflowType) {
                return $workflowType->getName();
            },
            $this->types
        );
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
    public function getType($typeName)
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
    public function hasType($typeName)
    {
        foreach ($this->types as $workflowType) {
            if ($workflowType->getName() === $typeName) {
                return true;
            }
        }

        return false;
    }
}
