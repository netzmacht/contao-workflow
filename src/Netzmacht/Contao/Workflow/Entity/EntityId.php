<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Entity;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;

class EntityId
{
    /**
     * @var int
     */
    private $identifier;

    /**
     * @var string
     */
    private $providerName;

    /**
     * @param int    $providerName
     * @param string $identifier
     */
    private function __construct($providerName, $identifier)
    {
        $this->providerName = $providerName;
        $this->identifier   = $identifier;
    }

    /**
     * @param ModelInterface $entity
     *
     * @return static
     */
    public static function fromEntity(ModelInterface $entity)
    {
        return new static($entity->getProviderName(), $entity->getId());
    }

    /**
     * @param $entityId
     *
     * @return static
     */
    public static function fromString($entityId)
    {
        list($providerName, $identifier) = explode('::', $entityId, 2);

        return new static($providerName, $identifier);
    }

    /**
     * @param $providerName
     * @param $entityId
     *
     * @return static
     */
    public static function fromScalars($providerName, $entityId)
    {
        return new static($providerName, $entityId);
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @param EntityId $entityId
     *
     * @return bool
     */
    public function equals(EntityId $entityId)
    {
        return ((string) $this == (string) $entityId);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->providerName . '::' . $this->identifier;
    }
}
