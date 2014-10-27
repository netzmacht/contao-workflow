<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Factory\Event;


use Netzmacht\Contao\Workflow\Manager;
use Symfony\Component\EventDispatcher\Event;

class CreateManagerEvent extends Event
{
    const NAME = 'workflow.factory.create-manager';

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param Manager $manager
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
    }
}
