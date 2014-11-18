<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Flow;

use Netzmacht\Workflow\Contao\Flow\Event\CreateWorkflowEvent;
use Netzmacht\Workflow\Contao\Model\WorkflowModel;
use Netzmacht\Workflow\Factory\Event\CreateManagerEvent;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ManagerBuilder extends AbstractBuilder implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            CreateManagerEvent::NAME => 'build'
        );
    }

    public function build(CreateManagerEvent $event)
    {
        if ($event->getManager()) {
            return;
        }

        $providerName = $event->getProviderName();
        $workflowType = $event->getWorkflowType();
        $manager      = $this->createManager();

        $this->createWorkflows($manager, $providerName, $workflowType);

        $event->setManager($manager);
    }

    /**
     * @return Manager
     */
    public function createManager()
    {
        return new Manager(
            $this->getService('workflow.factory.transition-handler'),
            $this->getService('workflow.state-repository')
        );
    }

    /**
     * Create workflows for a manager.
     *
     * @param Manager $manager
     * @param string  $providerName
     * @param string  $workflowType
     */
    private function createWorkflows(Manager $manager, $providerName, $workflowType)
    {
        if ($workflowType) {
            $collection = WorkflowModel::findByProviderAndType($providerName, $workflowType);
        } else {
            $collection = WorkflowModel::findByProvider($providerName);
        }

        while ($collection && $collection->next()) {
            $workflow = new Workflow(
                $collection->name,
                $collection->providerName,
                $collection->label,
                array(
                    'source' => static::SOURCE_DATABASE,
                    'model'  => $collection->current()
                )
            );

            $event = new CreateWorkflowEvent($workflow);
            $this->getService('event-dispatcher')->dispatch($event::NAME, $event);

            $manager->addWorkflow($workflow);
        }
    }
}
