<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Workflow\Entity;

use Netzmacht\Contao\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityId;

/**
 * Interface EntityFactory
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
interface EntityFactory
{
    /**
     * Check if factory supports the entity.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $data     The entity data.
     *
     * @return bool
     */
    public function supports(EntityId $entityId, $data): bool;

    /**
     * Create the entity.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $data     The entity data.
     *
     * @return Entity
     *
     * @throws UnsupportedEntity When no entity could be created.
     */
    public function create(EntityId $entityId, $data): Entity;
}
