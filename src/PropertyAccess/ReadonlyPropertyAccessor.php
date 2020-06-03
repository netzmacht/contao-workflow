<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

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
     * PropertyReadAccessor constructor.
     *
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
     *
     * @return bool
     */
    public function has(string $name) : bool
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
