<?php

namespace Netzmacht\Contao\Workflow\Flow;

class Context
{
    /**
     * @var array
     */
    private $params = array();

    /**
     * @var array
     */
    private $properties = array();


    /**
     * @param array $properties
     * @param array $params
     */
    public function __construct(array $properties = array(), array $params = array())
    {
        $this->properties = $properties;
        $this->params     = $params;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return null
     */
    public function getParam($name)
    {
        if ($this->hasParam($name)) {
            return $this->params[$name];
        }

        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasParam($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return null
     */
    public function getProperty($name)
    {
        if ($this->hasProperty($name)) {
            return $this->properties[$name];
        }

        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
