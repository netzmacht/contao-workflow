<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Model;

use Netzmacht\Contao\Workflow\Flow\State;

/**
 * Class StateRepository stores workflow states.
 *
 * @package Netzmacht\Contao\Workflow\Model
 */
class StateRepository
{
    /**
     * Find last worfklow state of an entity.
     *
     * @param string $providerName The provider/table name.
     * @param int    $entityId     The entity id.
     *
     * @return State
     */
    public function find($providerName, $entityId)
    {
        return State::init();
    }

    /**
     * Add a new state.
     *
     * @param State $state The new state.
     *
     * @return void
     */
    public function add(State $state)
    {
    }
}
