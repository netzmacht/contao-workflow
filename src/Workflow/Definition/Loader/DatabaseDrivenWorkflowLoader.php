<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader;

use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Definition;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateWorkflowEvent;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowRepository;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeRegistry;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class DatabaseDrivenWorkflowLoader
 */
final class DatabaseDrivenWorkflowLoader implements WorkflowLoader
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
     * Workflow type registry.
     *
     * @var WorkflowTypeRegistry
     */
    private $typeRegistry;

    /**
     * DatabaseDrivenWorkflowLoader constructor.
     *
     * @param RepositoryManager $repositoryManager Contao model repository manager.
     * @param WorkflowTypeRegistry $typeRegistry   Workflow type registry.
     * @param EventDispatcher $eventDispatcher     Event dispatcher.
     */
    public function __construct(
        RepositoryManager $repositoryManager,
        WorkflowTypeRegistry $typeRegistry,
        EventDispatcher $eventDispatcher
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->eventDispatcher   = $eventDispatcher;
        $this->typeRegistry      = $typeRegistry;
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
                    'workflow_' . $model->id,
                    (string) $model->providerName,
                    (string) ($model->label ?: $model->name),
                    array_merge(
                        $model->row(),
                        array(Definition::SOURCE => Definition::SOURCE_DATABASE)
                    )
                );

                $next = function () use ($workflow) {
                    $event = new CreateWorkflowEvent($workflow);
                    $this->eventDispatcher->dispatch($event::NAME, $event);
                };

                $this->typeRegistry->getType($model->type)->configure($workflow, $next);

                $workflows[] = $workflow;
            }
        }

        return $workflows;
    }
}
