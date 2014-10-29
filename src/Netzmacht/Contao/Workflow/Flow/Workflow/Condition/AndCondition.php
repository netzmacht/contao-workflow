<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Workflow\Condition;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;

/**
 * Class AndCondition matches if all child conditions matches.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Workflow
 */
class AndCondition extends ConditionCollection
{
    /**
     * {@inheritdoc}
     */
    public function match(Entity $entity)
    {
        foreach ($this->conditions as $condition) {
            if (!$condition->match($entity)) {
                return false;
            }
        }

        return true;
    }
}
