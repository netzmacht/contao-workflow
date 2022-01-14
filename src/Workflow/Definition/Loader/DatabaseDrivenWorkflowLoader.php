<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader;

use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowRepository;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Definition;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateWorkflowEvent;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Exception\DefinitionException;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeNotFound;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeRegistry;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Workflow;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;

use function array_merge;
use function assert;
use function sprintf;

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
        $workflowRepository = $this->repositoryManager->getRepository(WorkflowModel::class);
        assert($workflowRepository instanceof WorkflowRepository);
        $collection = $workflowRepository->findActive();
        $workflows  = [];

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
     * @throws WorkflowNotFound When workflow is not defined in the database.
     */
    public function loadWorkflowById(int $workflowId): Workflow
    {
        $workflowRepository = $this->repositoryManager->getRepository(WorkflowModel::class);
        assert($workflowRepository instanceof WorkflowRepository);
        $model = $workflowRepository->findOneBy(['.id=?'], [$workflowId]);

        if (! $model instanceof WorkflowModel) {
            throw new WorkflowNotFound(sprintf('Workflow with ID "%s" not found.', $workflowId));
        }

        return $this->createWorkflow($model);
    }

    /**
     * Create a workflow from the database model.
     *
     * @param WorkflowModel $model Databse workflow model.
     *
     * @throws WorkflowTypeNotFound When workflow type is not found.
     */
    public function createWorkflow(WorkflowModel $model): Workflow
    {
        $workflow = new Workflow(
            'workflow_' . $model->id,
            $model->providerName,
            $model->label,
            array_merge(
                $model->row(),
                [Definition::SOURCE => Definition::SOURCE_DATABASE]
            )
        );

        $next = function () use ($workflow): void {
            $event = new CreateWorkflowEvent($workflow);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        };

        $this->typeRegistry->getType($model->type)->configure($workflow, $next);

        return $workflow;
    }
}
