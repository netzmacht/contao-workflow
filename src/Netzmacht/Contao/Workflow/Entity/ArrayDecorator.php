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

class ArrayDecorator extends DefaultModel implements Entity
{
    /**
     * @var State
     */
    private $state;

    /**
     * @param array $properties
     * @param $providerName
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
     * @return State
     */
    public function getState()
    {
        return $this->getState();
    }

    /**
     * @param State $state
     *
     * @return mixed
     */
    public function transit(State $state)
    {
        $this->state = $state;
    }
}
