<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Workflow\Definition\Loader;

use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Workflow\Definition\Definition;
use Netzmacht\Contao\Workflow\Definition\Event\CreateWorkflowEvent;
use Netzmacht\Contao\Workflow\Model\Workflow\WorkflowModel;
use Netzmacht\Contao\Workflow\Model\Workflow\WorkflowRepository;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class DatabaseDrivenWorkflowLoader
 */
class DatabaseDrivenWorkflowLoader implements WorkflowLoader
{
    /**
     * Contao model repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * DatabaseDrivenWorkflowLoader constructor.
     *
     * @param RepositoryManager        $repositoryManager Contao model repository manager.
     * @param EventDispatcher $eventDispatcher   Event dispathe
     */
    public function __construct(RepositoryManager $repositoryManager, EventDispatcher $eventDispatcher)
    {
        $this->repositoryManager = $repositoryManager;
        $this->eventDispatcher   = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function load(): array
    {
        /** @var WorkflowRepository $workflowRepository */
        $workflowRepository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $collection         = $workflowRepository->findAll();
        $workflows          = [];

        if ($collection) {
            foreach ($collection as $model) {
                $workflow = new Workflow(
                    (string) $model->name,
                    (string) $model->providerName,
                    (string) ($model->label ?: $model->name),
                    array_merge(
                        $model->row(),
                        array(Definition::SOURCE => Definition::SOURCE_DATABASE)
                    )
                );

                $event = new CreateWorkflowEvent($workflow);
                $this->eventDispatcher->dispatch($event::NAME, $event);

                $workflows[] = $workflow;
            }
        }

        return $workflows;
    }
}
