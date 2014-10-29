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

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Contao\Workflow\Action;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;

/**
 * Class ModifyEntityAction modify entity properties during the transition.
 *
 * @package Netzmacht\Contao\Workflow\Action
 */
class ModifyEntityAction extends AbstractAction
{
    /**
     * Given values.
     *
     * @var array
     */
    private $values = array();

    /**
     * Data mapping between context params and entity properties.
     *
     * @var array
     */
    private $dataMapping = array();

    /**
     * Set a value.
     *
     * @param string $name  Value name.
     * @param mixed  $value Value being applied to entity.
     *
     * @return $this
     */
    public function setValue($name, $value)
    {
        $this->values[$name] = $value;

        return $this;
    }

    /**
     * Get a given value. Return null if not set.
     *
     * @param string $name Value name.
     *
     * @return mixed
     */
    public function getValue($name)
    {
        if ($this->hasValue($name)) {
            return $this->values[$name];
        }

        return null;
    }

    /**
     * Consider if value exists.
     *
     * @param string $name Value name.
     *
     * @return bool
     */
    public function hasValue($name)
    {
        return isset($this->values[$name]);
    }

    /**
     * Remove a value.
     *
     * @param string $name Value name.
     *
     * @return $this
     */
    public function removeValue($name)
    {
        unset($this->values[$name]);

        return $this;
    }

    /**
     * Add a data mapping.
     *
     * @param string $name The property name.
     *
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
     * Consider if data mapping exists.
     *
     * @param string $name The property name.
     *
     * @return bool
     */
    public function hasDataMapping($name)
    {
        return in_array($name, $this->dataMapping);
    }

    /**
     * Remove a data mapping.
     *
     * @param string $name The property name.
     *
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
     * Change property values during transition.
     *
     * @param Transition $transition Current transition.
     * @param Entity     $entity     Given entity.
     * @param Context    $context    Transition environment.
     *
     * @return void
     */
    public function transit(Transition $transition, Entity $entity, Context $context)
    {
        foreach ($this->dataMapping as $name) {
            $value = $context->getParam($name);

            $entity->setProperty($name, $value);
            $context->setProperty($name, $value);
        }

        foreach ($this->values as $name => $value) {
            $entity->setProperty($name, $value);
            $context->setProperty($name, $value);
        }
    }
}
