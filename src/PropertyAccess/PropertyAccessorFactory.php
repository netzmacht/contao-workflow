<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

/**
 * Interface PropertyAccessorFactory describes a factory for a specific property accessor
 */
interface PropertyAccessorFactory
{
    /**
     * Check if entity supports property access.
     *
     * @param mixed $object Given object.
     */
    public function supports($object): bool;

    /**
     * Create property accessor for a given object.
     *
     * @param mixed $object Given object.
     */
    public function create($object): PropertyAccessor;
}
