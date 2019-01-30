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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\EntityWithPropertyAccess;
use Netzmacht\Workflow\Flow\Item;

/**
 * Trait GetEntity provides method to get entity by making sure it implements interface EntityWithPropertyAccess
 */
trait GetEntity
{
    /**
     * Get the entity of the item and protect entity type.
     *
     * @param Item $item Workflow item.
     *
     * @return EntityWithPropertyAccess
     *
     * @hrows AssertionException If entity is not an Instance of
     *
     * @throws AssertionFailedException When the entity is not an instance of EntityWithPropertyAccess.
     */
    protected function getEntity(Item $item): EntityWithPropertyAccess
    {
        $entity = $item->getEntity();

        Assertion::isInstanceOf($entity, EntityWithPropertyAccess::class, 'Invalid entity given');

        return $entity;
    }
}
