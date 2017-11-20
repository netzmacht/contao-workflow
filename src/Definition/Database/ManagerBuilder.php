<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Definition\Database;

use Netzmacht\Contao\Workflow\Definition\Definition;
use Netzmacht\Contao\Workflow\Definition\Event\CreateWorkflowEvent;
use Netzmacht\Contao\Workflow\Manager\ContaoWorkflowManager as ContaoWorkflowManager;
use Netzmacht\Contao\Workflow\Model\WorkflowModel;
use Netzmacht\Contao\Workflow\ServiceContainerTrait;
use Netzmacht\Workflow\Factory\Event\CreateManagerEvent;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager\CachedManager;
use Netzmacht\Workflow\Manager\Manager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ManagerBuilder builds a manager instance.
 *
 * @package Netzmacht\Contao\Workflow\Definition\Builder
 */
class ManagerBuilder implements EventSubscriberInterface
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

        return new CachedManager(
            new ContaoWorkflowManager(
                $serviceProvider->getTransitionHandlerFactory(),
                $serviceProvider->getStateRepository(),
                $serviceProvider->getService('event-dispatcher')
            )
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
            $this->getServiceContainer()->getEventDispatcher()->dispatch($event::NAME, $event);

            $manager->addWorkflow($workflow);
        }
    }
}
