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
use Netzmacht\Workflow\Data\EntityId;

/**
 * Class DataEntity provides an entity implementation for simple array data
 */
class DatabaseEntity implements \IteratorAggregate, Entity
{
    /**
     * The entity id.
     *
     * @var EntityId
     */
    private $entityId;

    /**
     * The data.
     *
     * @var array
     */
    private $data;

    /**
     * DataArrayEntity constructor.
     *
     * @param EntityId $entityId The entity id.
     * @param array    $data     The data.
     */
    public function __construct(EntityId $entityId, array $data)
    {
        $this->entityId = $entityId;
        $this->data     = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getProviderName(): string
    {
        return $this->entityId->getProviderName();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->entityId->getIdentifier();
    }

    /**
     * {@inheritDoc}
     */
    public function getProperty(string $propertyName)
    {
        if (isset($this->data[$propertyName])) {
            return $this->data[$propertyName];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function setProperty(string $propertyName, $value): Entity
    {
        $this->data[$propertyName] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasProperty(string $propertyName): bool
    {
        return array_key_exists($propertyName, $this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function setProperties(array $properties): Entity
    {
        $this->data = $properties;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}
