<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Action;


use Netzmacht\Contao\Workflow\Action;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;

class ModifyEntityAction extends AbstractAction
{
    /**
     * @var array
     */
    private $values = array();

    /**
     * @var array
     */
    private $dataMapping = array();

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setValue($name, $value)
    {
        $this->values[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return null
     */
    public function getValue($name)
    {
        if ($this->hasValue($name)) {
            return $this->values[$name];
        }

        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasValue($name)
    {
        return isset($this->values[$name]);
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeValue($name)
    {
        unset($this->values[$name]);

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function addDataMapping($name)
    {
        if (!$this->hasDataMapping($name)) {
            $this->dataMapping[] = $name;
        }

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasDataMapping($name)
    {
        return in_array($name, $this->dataMapping);
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeDataMapping($name)
    {
        $key = array_search($name, $this->dataMapping);

        if ($key !== false) {
            unset($this->dataMapping[$key]);
        }

        return $this;
    }

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @param Context $context
     * @return void
     */
    public function transit(Transition $transition, Entity $entity, Context $context)
    {
        foreach ($this->dataMapping as $name) {
            $entity->setProperty($name, $context->getProperty($name));
        }

        foreach ($this->values as $name => $value) {
            $entity->setProperty($name, $value);
        }

    }
}
