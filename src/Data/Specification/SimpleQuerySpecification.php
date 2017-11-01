<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Data\Specification;

use ContaoCommunityAlliance\DcGeneral\Data\ConfigInterface as Config;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Contao\Workflow\Data\QuerySpecification;
use Netzmacht\Workflow\Util\Comparison;

/**
 * Class SimpleQuerySpecification.
 *
 * @package Netzmacht\Contao\Workflow\Data\Specification
 */
class SimpleQuerySpecification implements QuerySpecification
{
    const OPERATION_EQUALS = '=';
    const OPERATION_LESSER_THAN = '<';
    const OPERATION_GREATER_THAN = '>';
    const OPERATION_LIKE = 'LIKE';

    /**
     * Set of conditions.
     *
     * @var array
     */
    private $conditions = array();

    /**
     * Add a where condition.
     *
     * @param string $property  The property name.
     * @param mixed  $value     The property value.
     * @param string $operation The comparison operation.
     *
     * @return $this
     */
    public function where($property, $value, $operation = self::OPERATION_EQUALS)
    {
        $this->conditions[] = array(
            'propery'   => $property,
            'value'     => $value,
            'operation' => $operation
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(Config $config)
    {
        $config->setFilter($this->conditions);

        return $this;
    }

    /**
     * Check if entity is satisfied by
     * @param Entity $entity   The entity.
     * @param array  $condtion The condition.
     *
     * @return bool
     */
    private function satisfiesCondition(Entity $entity, $condtion)
    {
        if ($condtion['operation'] === static::OPERATION_EQUALS) {
            $operation = Comparison::EQUALS;
        } else {
            $operation = $condtion['operation'];
        }

        return Comparison::compare($entity->getProperty($condtion['property']), $condtion['value'], $operation);
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($entity)
    {
        if (!$entity instanceof Entity) {
            return false;
        }

        foreach ($this->conditions as $condtion) {
            if (!$this->satisfiesCondition($entity, $condtion)) {
                return false;
            }
        }

        return true;
    }
}
