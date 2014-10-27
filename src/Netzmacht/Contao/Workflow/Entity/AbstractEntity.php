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


use Netzmacht\Contao\Workflow\Flow\State;

abstract class AbstractEntity implements Entity
{
    /**
     * @var array
     */
    private $metaData = array();

    /**
     * @var State
     */
    private $state;

    /**
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param State $state
     *
     * @return mixed|void
     */
    public function transit(State $state)
    {
        $this->state = $state;
    }

    /**
     * Fetch meta information from model.
     *
     * @param string $metaName The meta information to retrieve.
     *
     * @return mixed The set meta information or null if undefined.
     */
    public function getMeta($metaName)
    {
        if (isset($this->metaData[$metaName])) {
            return $this->metaData[$metaName];
        }

        return null;
    }

    /**
     * Update meta information in the model.
     *
     * @param string $strMetaName The meta information name.
     *
     * @param mixed  $varValue    The meta information value to store.
     *
     * @return void
     */
    public function setMeta($strMetaName, $varValue)
    {
        $this->metaData[$strMetaName] = $varValue;
    }
}
