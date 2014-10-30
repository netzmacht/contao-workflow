<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Transition\Condition;

use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Transition\Condition;

/**
 * Class ConditionCollection contains child conditions which are called during match.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Transition\Condition
 */
abstract class ConditionCollection implements Condition
{
    /**
     * @var array|Condition[]
     */
    protected $conditions;

    /**
     * Add condition.
     *
     * @param Condition $condition
     *
     * @return $this
     */
    public function addCondition(Condition $condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * Remove condition from collection.
     *
     * @param Condition $condition
     *
     * @return $this
     */
    public function removeCondition(Condition $condition)
    {
        foreach ($this->conditions as $index => $value) {
            if ($value === $condition) {
                unset($this->conditions[$index]);
            }
        }

        return $this;
    }

    /**
     * Get child conditions
     *
     * @return array|Transition\Condition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}
