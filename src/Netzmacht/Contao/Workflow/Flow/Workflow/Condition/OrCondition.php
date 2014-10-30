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
use Netzmacht\Contao\Workflow\Flow\Workflow;

/**
 * Class OrCondition matches if any child conditions matches.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Workflow
 */
class OrCondition extends ConditionCollection
{
    /**
     * {@inheritdoc}
     */
    public function match(Workflow $workflow, Entity $entity)
    {
        foreach ($this->conditions as $condition) {
            if ($condition->match($workflow, $entity)) {
                return true;
            }
        }

        if ($this->conditions) {
            return true;
        }

        return false;
    }
}
