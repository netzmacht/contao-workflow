<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Entity;

use ContaoCommunityAlliance\DcGeneral\Data\DefaultModel;
use Netzmacht\Contao\WorkflowDeprecated\Entity;

class ArrayEntity extends DefaultModel implements Entity
{
    use WorkflowStateTrait;

    /**
     * @param array $properties
     * @param $providerName
     */
    function __construct(array $properties, $providerName)
    {
        $this->setPropertiesAsArray($properties);
        $this->setProviderName($providerName);
    }

} 