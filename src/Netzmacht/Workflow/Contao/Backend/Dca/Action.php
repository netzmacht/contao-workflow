<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Backend\Dca;

use Netzmacht\Workflow\Contao\Model\RoleModel;
use Netzmacht\Workflow\Contao\Model\TransitionModel;
use Netzmacht\Workflow\Contao\Backend\Event\GetWorkflowActionsEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Action is used for tl_workflow_action callbacks.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class Action
{
    /**
     * Get all available types.
     *
     * @param \DataContainer $dataContainer The datacontainer.
     *
     * @return array
     */
    public function getTypes($dataContainer)
    {
        $workflowModel = $this->getWorkflowModel($dataContainer);

        $eventDispatcher = $this->getEventDispatcher();
        $event           = new GetWorkflowActionsEvent($workflowModel);

        $eventDispatcher->dispatch($event::NAME, $event);

        return $event->getActions();
    }

    /**
     * Get workflow roles.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     */
    public function getRoles($dataContainer)
    {
        $workflowModel = $this->getWorkflowModel($dataContainer);
        $collection    = RoleModel::findBy('pid', $workflowModel->id, array('order' => 'label'));
        $options       = array();

        while ($collection && $collection->next()) {
            $options[$collection->id] = $collection->label;
        }

        return $options;
    }

    /**
     * Get the event dispatcher from the DIC.
     *
     * @return EventDispatcherInterface
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getEventDispatcher()
    {
        return $GLOBALS['container']['event-dispatcher'];
    }

    /**
     * Get the workflow model.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return \Model|\Model\Collection
     *
     * @throws \Exception If relation could not be resolved.
     */
    protected function getWorkflowModel($dataContainer)
    {
        $transitionModel = TransitionModel::findByPk($dataContainer->activeRecord->pid);
        $workflowModel   = $transitionModel->getRelated('pid');

        return $workflowModel;
    }
}
