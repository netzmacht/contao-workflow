<?php

namespace Netzmacht\Contao\Workflow\Flow;

class Step
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var array
     */
    private $allowedTransitions = array();

    /**
     * @var bool
     */
    private $final = false;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        if (!$this->label) {
            return $this->name;
        }

        return $this->label;
    }

    /**
     * @param mixed $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * @param boolean $final
     * @return $this
     */
    public function setFinal($final)
    {
        $this->final = (bool) $final;

        return $this;
    }

    /**
     * @param $transitionName
     * @return $this
     */
    public function allowTransition($transitionName)
    {
        if (!in_array($transitionName, $this->allowedTransitions)) {
            $this->allowedTransitions[] = $transitionName;
        }

        return $this;
    }

    /**
     * @param $transitionName
     * @return $this
     */
    public function disallowTransition($transitionName)
    {
        $key = array_search($transitionName, $this->allowedTransitions);

        if ($key !== false) {
           unset($this->allowedTransitions[$key]);
            $this->allowedTransitions = array_values($this->allowedTransitions);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedTransitions()
    {
        if($this->isFinal()) {
            return array();
        }

        return $this->allowedTransitions;
    }

    /**
     * @param $transitionName
     * @return bool
     */
    public function isTransitionAllowed($transitionName)
    {
        if ($this->isFinal()) {
            return false;
        }

        return in_array($transitionName, $this->allowedTransitions);
    }

}
