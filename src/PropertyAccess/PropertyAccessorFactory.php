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

/**
 * Interface PropertyAccessorFactory describes a factory for a specific property accessor
 */
interface PropertyAccessorFactory
{
    /**
     * Check if entity supports property access.
     *
     * @param mixed $object Given object.
     *
     * @return bool
     */
    public function supports($object) : bool;

    /**
     * Create property accessor for a given object.
     *
     * @param mixed $object Given object.
     *
     * @return PropertyAccessor
     */
    public function create($object) : PropertyAccessor;
}
