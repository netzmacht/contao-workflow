<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Action;


use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBag;
use Netzmacht\Contao\WorkflowDeprecated\Action;
use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Netzmacht\Contao\WorkflowDeprecated\Flow\Transition;
use Netzmacht\Contao\WorkflowDeprecated\View;

class ModifyEntityAction extends AbstractAction implements Action
{

    /**
     * @var array
     */
    private $data = array();

    /**
     * @var bool
     */
    private $trackChanges;

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isTrackChanges()
    {
        return $this->trackChanges;
    }

    /**
     * @param boolean $trackChanges
     * @return $this
     */
    public function setTrackChanges($trackChanges)
    {
        $this->trackChanges = (bool) $trackChanges;

        return $this;
    }

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @param PropertyValueBag $data
     * @return mixed
     */
    public function execute(Transition $transition, Entity $entity, PropertyValueBag $data)
    {
        $changes = $this->fetchData();

        if ($this->isTrackChanges()) {
            $old = array();

            foreach (array_keys($changes) as $name) {
                $old[$name] = $entity->getProperty($name);
            }

            $changedValues = $data->getPropertyValue('changes');
            $changedValues = array_merge((array) $changedValues, $changes);
            $data->setPropertyValue('changes', $changedValues);
        }

        $entity->setPropertiesAsArray($changes);
    }

    /**
     * @return array
     */
    private function fetchData()
    {
        $data = $this->data;

        if ($this->form) {
            $data = array_merge($data, $this->form->fetchAll());
        }

        return $data;
    }

} 