<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use ArrayAccess;

use function assert;
use function is_array;

/**
 * Factory creates instance of the array property accessor.
 */
final class ArrayPropertyAccessorFactory implements PropertyAccessorFactory
{
    /**
     * {@inheritDoc}
     */
    public function supports($object): bool
    {
        return is_array($object) || $object instanceof ArrayAccess;
    }

    /**
     * {@inheritDoc}
     */
    public function create($object): PropertyAccessor
    {
        assert(is_array($object) || $object instanceof ArrayAccess);

        return new ArrayPropertyAccessor($object);
    }
}
