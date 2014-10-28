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

use Netzmacht\Contao\Workflow\Model\State;

/**
 * Class AbstractEntity implements base Entity functionality.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
abstract class AbstractEntity implements Entity
{
    /**
     * Stored metadata.
     *
     * @var array
     */
    private $metaData = array();

    /**
     * Workflow state.
     *
     * @var State
     */
    private $state;

    /**
     * Get the workflow state.
     *
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Transit to a new workflow state.
     *
     * @param State $state New workflow state.
     *
     * @return void
     */
    public function transit(State $state)
    {
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($metaName)
    {
        if (isset($this->metaData[$metaName])) {
            return $this->metaData[$metaName];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMeta($strMetaName, $varValue)
    {
        $this->metaData[$strMetaName] = $varValue;
    }
}
