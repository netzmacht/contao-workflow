<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow;

use Netzmacht\Workflow\Manager\Manager;

/**
 * Class ManagerRegistry stores created managers.
 *
 * @package Netzmacht\Contao\Workflow
 */
class ManagerRegistry
{
    /**
     * Managers.
     *
     * @var array
     */
    private $managers = array();

    /**
     * Consider if manager isset.
     *
     * @param string      $providerName Provider name.
     * @param string|null $type         Manager type.
     *
     * @return bool
     */
    public function has($providerName, $type)
    {
        return isset($this->managers[$this->getName($providerName, $type)]);
    }

    /**
     * Get workflow manager.
     *
     * @param string      $providerName Provider name.
     * @param string|null $type         Manager type.
     *
     * @return Manager
     *
     * @throws \InvalidArgumentException If manager does not exists.
     */
    public function get($providerName, $type)
    {
        if ($this->has($providerName, $type)) {
            return $this->managers[$this->getName($providerName, $type)];
        }

        throw new \InvalidArgumentException(
            sprintf('Manager for proovider "%s" and type "%s" is not registered', $providerName, $type)
        );
    }

    /**
     * Set a new manager.
     *
     * @param string      $providerName Provider name.
     * @param string|null $type         Manager type.
     * @param Manager     $manager      Workflow manager.
     *
     * @return $this
     */
    public function set($providerName, $type, Manager $manager)
    {
        $this->managers[$this->getName($providerName, $type)] = $manager;

        return $this;
    }

    /**
     * Remove a manager.
     *
     * @param string      $providerName Provider name.
     * @param string|null $type         Manager type.
     *
     * @return $this
     */
    public function remove($providerName, $type)
    {
        unset($this->managers[$this->getName($providerName, $type)]);

        return $this;
    }

    /**
     * Get Name of provider and type.
     *
     * @param string      $providerName Provider name.
     * @param string|null $type         Manager type.
     *
     * @return string
     */
    private function getName($providerName, $type)
    {
        return $providerName . ($type ? ('.' . $type) : '__');
    }
}
