<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow;

/**
 * Class Configurable is the base class for each flow elements.
 *
 * @package Netzmacht\Contao\Workflow\Flow
 */
class Configurable
{
    /**
     * Configuration values.
     *
     * @var array
     */
    private $config = array();

    /**
     * Name of the element.
     *
     * @var string
     */
    private $name;

    /**
     * Label of the element.
     *
     * @var string
     */
    private $label;

    /**
     * Construct.
     *
     * @param string $name   Name of the element.
     * @param string $label  Label of the element.
     * @param array  $config Configuration values.
     */
    public function __construct($name, $label = null, array $config = array())
    {
        $this->name   = $name;
        $this->label  = $label ?: $name;
        $this->config = $config;
    }

    /**
     * Get element label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get element name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set a config value.
     *
     * @param string $name  Config property name.
     * @param mixed  $value Config property value.
     *
     * @return $this
     */
    public function setConfigValue($name, $value)
    {
        $this->config[$name] = $value;

        return $this;
    }

    /**
     * Get a config value.
     *
     * @param string $name    Config property name.
     * @param mixed  $default Default value which is returned if config is not set.
     *
     * @return null
     */
    public function getConfigValue($name, $default=null)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }

        return $default;
    }

    /**
     * Add multiple config properties.
     *
     * @param array $values Config values.
     *
     * @return $this
     */
    public function addConfig(array $values)
    {
        foreach ($values as $name => $value) {
            $this->setConfigValue($name, $values);
        }

        return $this;
    }

    /**
     * Consider if config value isset.
     *
     * @param string $name Config property.
     *
     * @return bool
     */
    public function hasConfig($name)
    {
        return isset($this->config[$name]);
    }

    /**
     * Remove a config property.
     *
     * @param string $name Config property name.
     *
     * @return $this
     */
    public function removeConfig($name)
    {
        unset($this->config[$name]);

        return $this;
    }

    /**
     * Get configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
