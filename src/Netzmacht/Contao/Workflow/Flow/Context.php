<?php

namespace Netzmacht\Contao\Workflow\Flow;

use ContaoCommunityAlliance\DcGeneral\InputProviderInterface as InputProvider;
use Netzmacht\Contao\Workflow\ErrorCollection;

/**
 * Class Context provides extra information for a transition.
 *
 * @package Netzmacht\Contao\Workflow\Flow
 */
class Context
{
    /**
     * Properties which will be stored as state data.
     *
     * @var array
     */
    private $properties = array();

    /**
     * The input provider.
     *
     * @var InputProvider
     */
    private $inputProvider;

    /**
     * Errors being raised during transition.
     *
     * @var ErrorCollection
     */
    private $errorCollection;

    /**
     * Params being passed.
     *
     * @var array
     */
    private $params;


    /**
     * Construct.
     *
     * @param InputProvider $inputProvider The input provider.
     * @param array         $properties    The properties to be stored.
     * @param array         $params        The given parameters.
     */
    public function __construct(InputProvider $inputProvider, array $properties = array(), array $params = array())
    {
        $this->inputProvider   = $inputProvider;
        $this->properties      = $properties;
        $this->params          = $params;
        $this->errorCollection = new ErrorCollection();
    }

    /**
     * Set a property value.
     *
     * @param string $name  Name of the property.
     * @param mixed  $value Value of the property.
     *
     * @return $this
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * Get the property value.
     *
     * @param string $name Property name.
     *
     * @return mixed
     */
    public function getProperty($name)
    {
        if ($this->hasProperty($name)) {
            return $this->properties[$name];
        }

        return null;
    }

    /**
     * Consider if property is set.
     *
     * @param string $name Property name.
     *
     * @return bool
     */
    public function hasProperty($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * Get all properties.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get the input provider.
     *
     * @return InputProvider
     */
    public function getInputProvider()
    {
        return $this->inputProvider;
    }

    /**
     * Consider if an error isset.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->errorCollection->hasErrors();
    }

    /**
     * Add a new error message.
     *
     * @param string $message The message.
     * @param array  $params  Message parameters.
     *
     * @return $this
     */
    public function addError($message, array $params = array())
    {
        $this->errorCollection->addError($message, $params);

        return $this;
    }

    /**
     * Get the error collection.
     *
     * @return ErrorCollection
     */
    public function getErrorCollection()
    {
        return $this->errorCollection;
    }

    /**
     * Set a parameter.
     *
     * @param string $name  Param name.
     * @param mixed  $value Param value.
     *
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Consider if a param isset.
     *
     * @param string $name Param name.
     *
     * @return bool
     */
    public function hasParam($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * Get all params.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get a param by name.
     *
     * @param string $name Param name.
     *
     * @return mixed
     */
    public function getParam($name)
    {
        if ($this->hasParam($name)) {
            return $this->params[$name];
        }

        return null;
    }

    /**
     * Set multiple params.
     *
     * @param array $params Array of params.
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }
}
