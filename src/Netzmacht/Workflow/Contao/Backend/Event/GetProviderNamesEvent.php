<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Backend\Event;

use Symfony\Component\EventDispatcher\Event;

class GetProviderNamesEvent extends Event
{
    const NAME = 'workflow.backend.get-provider-names';
    /**
     * Workflow type
     *
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $providerNames = array();

    /**
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getProviderNames()
    {
        return $this->providerNames;
    }

    /**
     * @param array $providerNames
     *
     * @return $this
     */
    public function setProviderNames($providerNames)
    {
        $this->providerNames = $providerNames;

        return $this;
    }
}
