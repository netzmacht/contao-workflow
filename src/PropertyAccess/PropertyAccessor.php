<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use IteratorAggregate;

interface PropertyAccessor extends IteratorAggregate
{
    /**
     * Set a property.
     *
     * @param string $name  Name of the property.
     * @param mixed  $value New value of the property.
     */
    public function set(string $name, $value): void;

    /**
     * Get the value of the property. If property not exists null is returned.
     *
     * @param string $name Name of the property.
     *
     * @return mixed
     */
    public function get(string $name);

    /**
     * Check if property exists for the object.
     *
     * @param string $name Name of the property.
     */
    public function has(string $name): bool;
}
