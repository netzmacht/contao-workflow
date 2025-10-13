<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use IteratorAggregate;

/** @extends IteratorAggregate<string, mixed> */
interface PropertyAccessor extends IteratorAggregate
{
    /**
     * Set a property.
     *
     * @param string $name  Name of the property.
     * @param mixed  $value New value of the property.
     */
    public function set(string $name, mixed $value): void;

    /**
     * Get the value of the property. If a property does not exist, null is returned.
     *
     * @param string $name Name of the property.
     */
    public function get(string $name): mixed;

    /**
     * Check if a property exists for the object.
     *
     * @param string $name Name of the property.
     */
    public function has(string $name): bool;
}
