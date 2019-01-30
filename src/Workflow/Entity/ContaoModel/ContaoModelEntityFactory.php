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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel;

use Contao\Model;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\Entity;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\EntityFactory;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityId;

/**
 * Class ContaoModelEntityFactory
 *
 * @package Netzmacht\ContaoWorkflowBundle\Entity
 */
final class ContaoModelEntityFactory implements EntityFactory
{
    /**
     * {@inheritDoc}
     */
    public function supports(EntityId $entityId, $data): bool
    {
        return is_a($data, Model::class);
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When no entity could be created.
     */
    public function create(EntityId $entityId, $data): Entity
    {
        if ($this->supports($entityId, $data)) {
            return new ContaoModelEntity($data);
        }

        throw UnsupportedEntity::forEntity($entityId);
    }
}
