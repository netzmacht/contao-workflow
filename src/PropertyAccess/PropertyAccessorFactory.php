<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

/**
 * Interface PropertyAccessorFactory describes a factory for a specific property accessor
 */
interface PropertyAccessorFactory
{
    /**
     * Check if the entity supports property access.
     *
     * @param mixed $object Given object.
     */
    public function supports(mixed $object): bool;

    /**
     * Create a property accessor for a given object.
     *
     * @param mixed $object Given object.
     */
    public function create(mixed $object): PropertyAccessor;
}
