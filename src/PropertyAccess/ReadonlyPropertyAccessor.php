<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use Contao\StringUtil;

/**
 * The property read accessor limits access for read access.
 */
final class ReadonlyPropertyAccessor
{
    /**
     * Property accessor.
     *
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param PropertyAccessor $propertyAccessor Property accessor.
     */
    public function __construct(PropertyAccessor $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * Check if property exists for the object.
     *
     * @param string $name Name of the property.
     */
    public function has(string $name): bool
    {
        return $this->propertyAccessor->has($name);
    }

    /**
     * Get the deserialized value of the property. If property not exists null is returned.
     *
     * @param string $name Name of the property.
     *
     * @return mixed
     */
    public function get(string $name)
    {
        return StringUtil::deserialize($this->propertyAccessor->get($name));
    }

    /**
     * Get the raw value of the property. If property not exists null is returned.
     *
     * @param string $name Name of the property.
     *
     * @return mixed
     */
    public function raw(string $name)
    {
        return $this->propertyAccessor->get($name);
    }
}
