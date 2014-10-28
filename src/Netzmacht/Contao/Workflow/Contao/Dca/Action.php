<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Contao\Dca;


use Netzmacht\Contao\Workflow\Contao\Dca\Event\GetWorkflowTypesEvent;
use Netzmacht\Contao\Workflow\Contao\Model\RoleModel;
use Netzmacht\Contao\Workflow\Contao\Model\TransitionModel;
use NotificationCenter\Model\Notification;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Action
{
    public function getTypes($dataContainer)
    {
        $workflowModel = $this->getWorkflowModel($dataContainer);

        $eventDispatcher = $this->getEventDispatcher();
        $event           = new GetWorkflowTypesEvent($workflowModel);

        $eventDispatcher->dispatch($event::NAME, $event);

        return $event->getTypes();
    }

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
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        return $GLOBALS['container']['event-dispatcher'];
    }

    /**
     * @param $dataContainer
     *
     * @return \Model|\Model\Collection
     * @throws \Exception
     */
    protected function getWorkflowModel($dataContainer)
    {
        $transitionModel = TransitionModel::findByPk($dataContainer->activeRecord->pid);
        $workflowModel   = $transitionModel->getRelated('pid');

        return $workflowModel;
    }
}
