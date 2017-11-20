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
 * Class DelegatingEntityFactory.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
class DelegatingEntityFactory implements EntityFactory
{
    /**
     * Entity factories.
     *
     * @var EntityFactory[]
     */
    private $factories;

    /**
     * DelegatingEntityFactory constructor.
     *
     * @param EntityFactory[] $factories Entity factories.
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(EntityId $entityId, $data): bool
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($entityId, $data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When no entity could be created.
     */
    public function create(EntityId $entityId, $data): Entity
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($entityId, $data)) {
                return $factory->create($entityId, $data);
            }
        }

        throw UnsupportedEntity::forEntity($entityId);
    }
}
