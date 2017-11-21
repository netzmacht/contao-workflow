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

namespace Netzmacht\Contao\Workflow\Entity\Database;

use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Entity\EntityFactory;
use Netzmacht\Workflow\Data\EntityId;

/**
 * Class DataEntityFactory
 */
class DatabaseEntityFactory implements EntityFactory
{
    /**
     * {@inheritDoc}
     */
    public function supports(EntityId $entityId, $data): bool
    {
        return is_array($data);
    }

    /**
     * {@inheritDoc}
     */
    public function create(EntityId $entityId, $data): Entity
    {
        return new DatabaseEntity($entityId, $data);
    }
}
