<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity;

/**
 * Interface WithPropertyAccess is meant for
 */
interface EntityWithPropertyAccess extends Entity, \Traversable
{
    /**
     * Get the id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get the property value.
     *
     * @param string $propertyName Property name.
     *
     * @return mixed
     */
    public function getProperty(string $propertyName);

    /**
     * Set a property.
     *
     * @param string $propertyName The property name.
     * @param mixed  $value        The property value.
     *
     * @return $this
     */
    public function setProperty(string $propertyName, $value): self;

    /**
     * Check if a property name exist.
     *
     * @param string $propertyName Property name.
     *
     * @return bool
     */
    public function hasProperty(string $propertyName): bool;

    /**
     * Set the properties from an array map.
     *
     * @param array $properties Properties.
     *
     * @return $this
     */
    public function setProperties(array $properties): self;
}
