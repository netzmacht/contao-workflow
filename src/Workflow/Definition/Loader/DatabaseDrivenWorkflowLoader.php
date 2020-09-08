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
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Exception\DefinitionException;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeNotFound;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeRegistry;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Workflow;
use Psr\Log\LoggerInterface;
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
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DatabaseDrivenWorkflowLoader constructor.
     *
     * @param RepositoryManager    $repositoryManager Contao model repository manager.
     * @param WorkflowTypeRegistry $typeRegistry      Workflow type registry.
     * @param EventDispatcher      $eventDispatcher   Event dispatcher.
     * @param LoggerInterface      $logger            Logger.
     */
    public function __construct(
        RepositoryManager $repositoryManager,
        WorkflowTypeRegistry $typeRegistry,
        EventDispatcher $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->eventDispatcher   = $eventDispatcher;
        $this->typeRegistry      = $typeRegistry;
        $this->logger            = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function load(): array
    {
        /** @var WorkflowRepository $workflowRepository */
        $workflowRepository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $collection         = $workflowRepository->findActive();
        $workflows          = [];

        if ($collection) {
            foreach ($collection as $model) {
                try {
                    $workflows[] = $this->createWorkflow($model);
                } catch (WorkflowTypeNotFound $exception) {
                    $this->logger->error(
                        sprintf(
                            'Creating workflow for model "%s" failed with message: %s',
                            $model->id,
                            $exception->getMessage()
                        )
                    );
                } catch (DefinitionException $exception) {
                    $this->logger->error(
                        sprintf(
                            'Creating workflow for model "%s" failed with message: %s',
                            $model->id,
                            $exception->getMessage()
                        )
                    );
                }
            }
        }

        return $workflows;
    }

    /**
     * Load a workflow by id.
     *
     * @param int $workflowId The workflow id.
     *
     * @return Workflow
     *
     * @throws WorkflowNotFound When workflow is not defined in the database.
     */
    public function loadWorkflowById(int $workflowId): Workflow
    {
        /** @var WorkflowRepository $workflowRepository */
        $workflowRepository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $model              = $workflowRepository->findOneBy(['.id=?'], [$workflowId]);

        if (!$model) {
            throw new WorkflowNotFound(sprintf('Workflow with ID "%s" not found.', $workflowId));
        }

        return $this->createWorkflow($model);
    }

    /**
     * Create a workflow from the database model.
     *
     * @param WorkflowModel $model Databse workflow model.
     *
     * @return Workflow
     *
     * @throws WorkflowTypeNotFound When workflow type is not found.
     */
    public function createWorkflow(WorkflowModel $model): Workflow
    {
        $workflow = new Workflow(
            'workflow_' . $model->id,
            (string) $model->providerName,
            (string) $model->label,
            array_merge(
                $model->row(),
                [Definition::SOURCE => Definition::SOURCE_DATABASE]
            )
        );

        $next = function () use ($workflow) {
            $event = new CreateWorkflowEvent($workflow);
            $this->eventDispatcher->dispatch($event::NAME, $event);
        };

        $this->typeRegistry->getType($model->type)->configure($workflow, $next);

        return $workflow;
    }
}
