<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Workflow\Contao\Data;

use Assert\Assertion;
use ContaoCommunityAlliance\DcGeneral\Data\CollectionInterface;
use ContaoCommunityAlliance\DcGeneral\Data\DataProviderInterface as DataProvider;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use Netzmacht\Workflow\Data\EntityRepository as WorkflowEntityRepository;
use Netzmacht\Workflow\Data\Specification;

/**
 * Class EntityRepository stores an entity.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
class EntityRepository implements WorkflowEntityRepository
{
    /**
     * The used data provider.
     *
     * @var DataProvider
     */
    private $provider;

    /**
     * Construct.
     *
     * @param DataProvider $provider The data provider.
     */
    public function __construct(DataProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Add an entity to the repository.
     *
     * @param Entity $entity The new entity.
     *
     * @return void
     *
     * @throws \InvalidArgumentException If an invalid entity type is given.
     */
    public function add($entity)
    {
        Assertion::isInstanceOf(
            $entity,
            'ContaoCommunityAlliance\DcGeneral\Data\ModelInterface',
            'Entity repository requires an instance of the ModelInterface'
        );

        $this->provider->save($entity);
    }

    /**
     * Find an entity by id.
     *
     * @param int $entityId The Entity id.
     *
     * @return Entity
     *
     * @throws \InvalidArgumentException If no entity were found.
     */
    public function find($entityId)
    {
        $config = $this->provider->getEmptyConfig();
        $config->setId($entityId);

        $model = $this->provider->fetch($config);

        if (!$model) {
            throw new \InvalidArgumentException(sprintf('Could not find entity "%s"', $entityId));
        }

        return $model;
    }

    /**
     * Find by a specification.
     *
     * It is highly recommend to pass a QuerySpecification. Otherwise all items have to be loaded!.
     *
     * @param Specification $specification The specification.
     *
     * @return array|CollectionInterface|ModelInterface[]|\string[]
     */
    public function findBySpecification(Specification $specification)
    {
        if ($specification instanceof QuerySpecification) {
            $config = $this->provider->getEmptyConfig();
            $specification->prepare($config);

            return $this->provider->fetchAll($config);
        }

        $result   = $this->provider->fetchAll($this->provider->getEmptyConfig());
        $filtered = array();

        foreach ($result as $entity) {
            if ($specification->isSatisfiedBy($entity)) {
                $filtered[] = $entity;
            }
        }

        return $filtered;
    }
}
