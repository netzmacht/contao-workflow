<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Definition\Database;

use Netzmacht\Workflow\Contao\Definition\Definition;
use Netzmacht\Workflow\Contao\Definition\Event\CreateWorkflowEvent;
use Netzmacht\Workflow\Contao\Model\WorkflowModel;
use Netzmacht\Workflow\Contao\ServiceContainerTrait;
use Netzmacht\Workflow\Factory\Event\CreateManagerEvent;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Contao\Manager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ManagerBuilder builds a manager instance.
 *
 * @package Netzmacht\Workflow\Contao\Definition\Builder
 */
class ManagerBuilder  implements EventSubscriberInterface
{
    use ServiceContainerTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateManagerEvent::NAME => 'build'
        );
    }

    /**
     * Build the manager.
     *
     * @param CreateManagerEvent $event The event subscribed.
     *
     * @return void
     */
    public function build(CreateManagerEvent $event)
    {
        $providerName = $event->getProviderName();
        $workflowType = $event->getWorkflowType();
        $manager      = $this->createManager();

        $this->createWorkflows($manager, $providerName, $workflowType);

        $event->setManager($manager);
    }

    /**
     * Create a new manager.
     *
     * @return Manager
     */
    public function createManager()
    {
        $serviceProvider = $this->getServiceProvider();

        return new Manager(
            $serviceProvider->getTransitionHandlerFactory(),
            $serviceProvider->getStateRepository(),
            $serviceProvider->getEntityFactory()
        );
    }

    /**
     * Create workflows for a manager.
     *
     * @param Manager $manager      The workflow manager.
     * @param string  $providerName The provider name.
     * @param string  $workflowType THe workflow type.
     *
     * @return void
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
                array_merge(
                    $collection->row(),
                    array(Definition::SOURCE => Definition::SOURCE_DATABASE)
                )
            );

            $event = new CreateWorkflowEvent($workflow);
            $this->getService('event-dispatcher')->dispatch($event::NAME, $event);

            $manager->addWorkflow($workflow);
        }
    }
}
