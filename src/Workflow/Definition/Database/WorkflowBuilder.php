<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Database;

use Contao\FilesModel;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Model\Action\ActionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Action\ActionRepository;
use Netzmacht\ContaoWorkflowBundle\Model\Step\StepModel;
use Netzmacht\ContaoWorkflowBundle\Model\Step\StepRepository;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionRepository;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Definition;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateStepEvent;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateTransitionEvent;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateWorkflowEvent;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Exception\DefinitionException;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionFactory;
use Netzmacht\Workflow\Flow\Condition\Workflow\ProviderNameCondition;
use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;

use function array_merge;
use function assert;
use function sprintf;

/**
 * Class WorkflowBuilder builds an workflow.
 */
final class WorkflowBuilder
{
    /**
     * Workflow steps.
     *
     * @var Step[]
     */
    private $steps = [];

    /**
     * Workflow transitions.
     *
     * @var Transition[]
     */
    private $transitions = [];

    /**
     * Contao model repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Action factory.
     *
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * Configuration of available transition types.
     *
     * @var array<string,array<string,mixed>>
     */
    private $transitionTypes;

    /**
     * @param RepositoryManager                 $repositoryManager Contao model repository manager.
     * @param ActionFactory                     $actionFactory     Action factory.
     * @param array<string,array<string,mixed>> $transitionTypes   Configuration of available transition types.
     */
    public function __construct(
        RepositoryManager $repositoryManager,
        ActionFactory $actionFactory,
        array $transitionTypes
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->actionFactory     = $actionFactory;
        $this->transitionTypes   = $transitionTypes;
    }

    /**
     * Handle the create workflow event.
     *
     * @param CreateWorkflowEvent $event           The event being subscribed.
     * @param string              $eventName       The event name.
     * @param EventDispatcher     $eventDispatcher The event dispatcher.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createWorkflow(
        CreateWorkflowEvent $event,
        string $eventName,
        EventDispatcher $eventDispatcher
    ): void {
        $workflow = $event->getWorkflow();

        if ($workflow->getConfigValue(Definition::SOURCE) !== Definition::SOURCE_DATABASE) {
            return;
        }

        $this->createProviderNameCondition($workflow);
        $this->createSteps($workflow, $eventDispatcher);
        $this->createTransitions($workflow, $eventDispatcher);
        $this->createProcess($workflow);

        $this->resetBuilder();
    }

    /**
     * Create the provider name condition.
     *
     * @param Workflow $workflow Workflow.
     */
    private function createProviderNameCondition(Workflow $workflow): void
    {
        $workflow->addCondition(
            new ProviderNameCondition($workflow->getProviderName())
        );
    }

    /**
     * Create the steps.
     *
     * @param Workflow        $workflow        The current workflow.
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     */
    private function createSteps(Workflow $workflow, EventDispatcher $eventDispatcher): void
    {
        $repository = $this->repositoryManager->getRepository(StepModel::class);
        assert($repository instanceof StepRepository);
        $collection = $repository->findByWorkflow((int) $workflow->getConfigValue('id'));

        if (! $collection) {
            return;
        }

        while ($collection->next()) {
            $model = $collection->current();
            assert($model instanceof StepModel);
            $step = new Step(
                'step_' . $model->id,
                $model->label,
                array_merge(
                    $model->row(),
                    [Definition::SOURCE => Definition::SOURCE_DATABASE]
                ),
                $workflow->getName()
            );

            $step->setFinal((bool) $model->final);

            if ($model->limitPermission && $model->permission !== '') {
                $step->setPermission(Permission::fromString($model->permission));
            }

            $workflow->addStep($step);

            $event = new CreateStepEvent($workflow, $step);
            $eventDispatcher->dispatch($event, $event::NAME);

            $this->steps[$model->id] = $step;
        }
    }

    /**
     * Create transitions from database.
     *
     * @param Workflow        $workflow        The current workflow.
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     *
     * @throws DefinitionException If a target step is defined which does not exiss.
     */
    private function createTransitions(Workflow $workflow, EventDispatcher $eventDispatcher): void
    {
        $repository = $this->repositoryManager->getRepository(TransitionModel::class);
        assert($repository instanceof TransitionRepository);
        $collection = $repository->findActiveByTransition((int) $workflow->getConfigValue('id'));

        if (! $collection) {
            return;
        }

        foreach ($collection as $model) {
            $this->transitions[$model->id] = $this->createTransition($workflow, $model, $eventDispatcher);
        }
    }

    /**
     * Load actions for all loaded transitions.
     *
     * @param Transition $transition The workflow transition.
     */
    private function createActions(Transition $transition): void
    {
        $repository = $this->repositoryManager->getRepository(ActionModel::class);
        assert($repository instanceof ActionRepository);
        $collection = $repository->findActiveByTransition((int) $transition->getConfigValue('id'));

        if (! $collection) {
            return;
        }

        foreach ($collection as $model) {
            $type = $model->type;
            if ($type === 'reference') {
                $model = $model->getRelated('reference');
                if (! $model instanceof ActionModel) {
                    continue;
                }

                $type = $model->type;
            }

            $action = $this->actionFactory->create($model->type, $model->row(), $transition);

            if ($this->actionFactory->isPostAction($type)) {
                $transition->addPostAction($action);
            } else {
                $transition->addAction($action);
            }
        }
    }

    /**
     * Create a new transition.
     *
     * @param Workflow        $workflow        The workflow.
     * @param TransitionModel $model           The transition model.
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     *
     * @throws DefinitionException When invalid configuration exists.
     */
    private function createTransition(
        Workflow $workflow,
        TransitionModel $model,
        EventDispatcher $eventDispatcher
    ): Transition {
        // Unknown transition type, skip it.
        if (! isset($this->transitionTypes[$model->type])) {
            throw new DefinitionException(sprintf('Unsupported transition type "%s"', $model->type));
        }

        $this->guardTargetStepExists($model);
        $data = $this->getDataFromModel($model);

        $transition = new Transition(
            'transition_' . $model->id,
            $workflow,
            $this->steps[$model->stepTo] ?? null,
            $model->label,
            array_merge(
                $data,
                [Definition::SOURCE => Definition::SOURCE_DATABASE]
            )
        );

        if ($model->limitPermission && $model->permission !== '') {
            $transition->setPermission(Permission::fromString($model->permission));
        }

        $workflow->addTransition($transition);

        if ($this->transitionTypes[$model->type]['actions']) {
            $this->createActions($transition);
        }

        $event = new CreateTransitionEvent($transition);
        $eventDispatcher->dispatch($event, $event::NAME);

        return $transition;
    }

    /**
     * Create Workflow process.
     *
     * @param Workflow $workflow The current workflow.
     */
    private function createProcess(Workflow $workflow): void
    {
        $process = StringUtil::deserialize($workflow->getConfigValue('process'), true);

        foreach ($process as $definition) {
            // pass not created transitions. useful to avoid errors when a transition got disabled
            if (! isset($this->transitions[$definition['transition']])) {
                continue;
            }

            if ($definition['step'] === 'start') {
                $workflow->setStartTransition($this->transitions[$definition['transition']]->getName());
            } else {
                $step       = $this->steps[$definition['step']];
                $transition = $this->transitions[$definition['transition']];

                $step->allowTransition($transition->getName());
            }
        }
    }

    /**
     * Reset the builder cache.
     */
    private function resetBuilder(): void
    {
        $this->transitions = [];
        $this->steps       = [];
    }

    /**
     * Guard that the target step of a transition exists.
     *
     * @param TransitionModel $model The transition model.
     *
     * @throws DefinitionException When a target step is required but it does not exist.
     */
    private function guardTargetStepExists(TransitionModel $model): void
    {
        if ($this->transitionTypes[$model->type]['step'] && ! isset($this->steps[$model->stepTo])) {
            throw new DefinitionException(
                sprintf(
                    'Transition ID "%s" refers to step "%s" which does not exists.',
                    $model->id,
                    $model->stepTo
                )
            );
        }
    }

    /**
     * Get the data from the model.
     *
     * @param TransitionModel $model The transition model.
     *
     * @return array<string,mixed>
     */
    private function getDataFromModel(TransitionModel $model): array
    {
        $data = $model->row();

        if ($data['icon']) {
            /** @psalm-suppress UndefinedInterfaceMethod */
            $file         = $this->repositoryManager->getRepository(FilesModel::class)->findByUuid($data['icon']);
            $data['icon'] = $file ? $file->path : null;
        }

        return $data;
    }
}
