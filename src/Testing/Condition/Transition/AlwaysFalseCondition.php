<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2020 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Testing\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

final class AlwaysFalseCondition implements Condition
{
    private $callCount = 0;

    /**
     * Gets the number of calls to the `match` function.
     *
     * @return int
     */
    public function getCallCount(): int
    {
        return $this->callCount;
    }

    /**
     * @inheritDoc
     */
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $this->callCount++;
        return false;
    }
}