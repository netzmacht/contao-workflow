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
use Netzmacht\Contao\Workflow\Flow\State;

/**
 * Interface Entity describes the contract for workflow entities.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
interface Entity extends ModelInterface
{
    /**
     * Get the current workflow state.
     *
     * @return State
     */
    public function getState();

    /**
     * Transit entity to a new state.
     *
     * @param State $state New state.
     *
     * @return void
     */
    public function transit(State $state);
}
