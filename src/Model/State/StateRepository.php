<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\State;

use Contao\StringUtil;
use Contao\Validator;
use DateTime;
use DateTimeImmutable;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository as WorkflowStateRepository;
use Netzmacht\Workflow\Flow\State;
use Override;
use ReflectionObject;

use function is_array;
use function json_decode;
use function json_encode;
use function time;

/**
 * Class StateRepository manages workflow states.
 */
final class StateRepository implements WorkflowStateRepository
{
    /**
     * Contao model repository manager.
     */
    private RepositoryManager $repositoryManager;

    /** @param RepositoryManager $repositoryManager Contao model repository manager. */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Find the last workflow state of an entity.
     *
     * @param EntityId $entityId The entity id.
     *
     * @return State[]|iterable
     */
    #[Override]
    public function find(EntityId $entityId): iterable
    {
        $repository = $this->repositoryManager->getRepository(StateModel::class);
        $collection = $repository->findBy(
            ['.entityId=?'],
            [(string) $entityId],
            ['order' => 'tl_workflow_state.reachedAt'],
        );

        $states = [];
        foreach ($collection ?: [] as $model) {
            $states[] = $this->createState($model);
        }

        return $states;
    }

    /**
     * Add a new state.
     *
     * @param State $state The new state.
     */
    #[Override]
    public function add(State $state): void
    {
        // state is immutable. only store new states.
        if ($state->getStateId() !== null) {
            return;
        }

        $model = $this->convertStateToModel($state);
        $this->repositoryManager->getRepository(StateModel::class)->save($model);

        // dynamically add state id.
        $reflector = new ReflectionObject($state);
        $property  = $reflector->getProperty('stateId');
        $property->setValue($state, $model->id);
    }

    /**
     * Convert a state object to model representation.
     *
     * @param State $state The state being persisted.
     */
    private function convertStateToModel(State $state): StateModel
    {
        $model = new StateModel();

        $model->workflowName       = $state->getStartWorkflowName();
        $model->entityId           = (string) $state->getEntityId();
        $model->transitionName     = $state->getTransitionName();
        $model->stepName           = $state->getStepName();
        $model->success            = $state->isSuccessful();
        $model->errors             = $this->serialize($state->getErrors());
        $model->data               = $this->serialize($state->getData());
        $model->reachedAt          = $state->getReachedAt()->getTimestamp();
        $model->targetWorkflowName = $state->getTargetWorkflowName();
        $model->tstamp             = time();

        return $model;
    }

    /**
     * Create the state object.
     *
     * @param StateModel $model The state model.
     */
    private function createState(StateModel $model): State
    {
        $reachedAt = new DateTime();
        $reachedAt->setTimestamp((int) $model->reachedAt);
        $reachedAt = DateTimeImmutable::createFromMutable($reachedAt);

        /** @psalm-suppress ArgumentTypeCoercion */
        return new State(
            EntityId::fromString($model->entityId),
            $model->workflowName,
            $model->transitionName,
            $model->stepName,
            (bool) $model->success,
            (array) json_decode($model->data, true),
            $reachedAt,
            (array) json_decode($model->errors, true),
            (int) $model->id,
            $model->targetWorkflowName,
        );
    }

    /**
     * Serialize data.
     *
     * @param mixed $data The data being serialized.
     */
    private function serialize(mixed $data): string
    {
        $data = $this->prepareSerialize($data);

        return (string) json_encode($data);
    }

    /**
     * Prepare serialization.
     *
     * @param mixed $data The data being serialized.
     */
    private function prepareSerialize(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->prepareSerialize($value);
            }
        } elseif (Validator::isBinaryUuid($data)) {
            $data = StringUtil::binToUuid($data);
        }

        return $data;
    }
}
