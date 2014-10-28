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

use ContaoCommunityAlliance\DcGeneral\Data\DefaultModel;
use Netzmacht\Contao\Workflow\Flow\State;

/**
 * Class ArrayDecorator implements an Entity for wrapping an array and treat it as model.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
class ArrayDecorator extends DefaultModel implements Entity
{
    /**
     * Workflow state.
     *
     * @var State
     */
    private $state;

    /**
     * Construct.
     *
     * @param array  $properties   The model row as array.
     * @param string $providerName The table/provider name.
     */
    public function __construct(array $properties, $providerName)
    {
        if (isset($properties['id'])) {
            $this->setID($properties['id']);
        }

        $this->setPropertiesAsArray($properties);
        $this->setProviderName($providerName);
    }

    /**
     * Get the workflow state.
     *
     * @return State
     */
    public function getState()
    {
        return $this->getState();
    }

    /**
     * Transit the workflow state.
     *
     * @param State $state New workflow state.
     *
     * @return void
     */
    public function transit(State $state)
    {
        $this->state = $state;
    }
}
