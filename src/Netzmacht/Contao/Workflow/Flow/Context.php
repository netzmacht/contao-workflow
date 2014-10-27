<?php

namespace Netzmacht\Contao\Workflow\Flow;

use ContaoCommunityAlliance\DcGeneral\InputProviderInterface as InputProvider;

class Context
{
    /**
     * @var array
     */
    private $properties = array();

    /**
     * @var InputProvider
     */
    private $inputProvider;


    /**
     * @param InputProvider $inputProvider
     * @param array         $properties
     *
     */
    public function __construct(InputProvider $inputProvider, array $properties = array())
    {
        $this->inputProvider = $inputProvider;
        $this->properties    = $properties;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     *
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
     *
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

    /**
     * @return InputProvider
     */
    public function getInputProvider()
    {
        return $this->inputProvider;
    }
}
