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

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use IteratorAggregate;

/**
 * Interface PropertyAccessor
 */
interface PropertyAccessor extends IteratorAggregate
{
    /**
     * Check if entity supports property access.
     *
     * @param mixed $object Given object.
     *
     * @return bool
     */
    public static function supports($object): bool;

    /**
     * Create property accessor for a given object.
     *
     * @param mixed $object Given object.
     *
     * @return PropertyAccessor
     */
    public static function create($object): self;

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
     *
     * @return bool
     */
    public function has(string $name): bool;
}
