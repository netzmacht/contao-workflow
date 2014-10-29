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

/**
 * Class CreateManagerEvent is dispatched when a workflow manager is created.
 *
 * @package Netzmacht\Contao\Workflow\Factory\Event
 */
class CreateManagerEvent extends Event
{
    const NAME = 'workflow.factory.createRepository-manager';

    /**
     * The created manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Workflow type.
     *
     * @var string
     */
    private $type;

    /**
     * Construct.
     *
     * @param string $type Workflow type.
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Get workflow type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the manager.
     *
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set the created manager.
     *
     * @param Manager $manager The created manager.
     *
     * @return $this
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;

        return $this;
    }
}
