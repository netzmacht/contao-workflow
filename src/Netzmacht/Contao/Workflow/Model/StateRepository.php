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

use Netzmacht\Contao\Workflow\Model\State;

/**
 * Interface StateRepository stores workflow states.
 *
 * @package Netzmacht\Contao\Workflow\Model
 */
interface StateRepository
{
    /**
     * Find last worfklow state of an entity.
     *
     * @param string $providerName The provider/table name.
     * @param int    $entityId     The entity id.
     * @param bool   $successful   Only get latest succesful.
     *
     * @return State
     */
    public function find($providerName, $entityId, $successful = true);

    /**
     * Add a new state.
     *
     * @param State $state The new state.
     *
     * @return void
     */
    public function add(State $state);
}
