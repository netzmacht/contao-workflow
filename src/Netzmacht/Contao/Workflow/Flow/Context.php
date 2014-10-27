<?php

namespace Netzmacht\Contao\Workflow\Flow;

use ContaoCommunityAlliance\DcGeneral\InputProviderInterface as InputProvider;
use Netzmacht\Contao\Workflow\ErrorCollection;

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
     * @var ErrorCollection
     */
    private $errorCollection;

    /**
     * @var array
     */
    private $params;


    /**
     * @param InputProvider $inputProvider
     * @param array         $properties
     * @param array         $params
     */
    public function __construct(InputProvider $inputProvider, array $properties = array(), array $params = array())
    {
        $this->inputProvider   = $inputProvider;
        $this->properties      = $properties;
        $this->params          = $params;
        $this->errorCollection = new ErrorCollection();
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

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return $this->errorCollection->hasErrors();
    }

    /**
     * @param       $message
     * @param array $params
     *
     * @return $this
     */
    public function addError($message, array $params = array())
    {
        $this->errorCollection->addError($message, $params);

        return $this;
    }

    /**
     * @return ErrorCollection
     */
    public function getErrorCollection()
    {
        return $this->errorCollection;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     *
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
     *
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
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }
}
