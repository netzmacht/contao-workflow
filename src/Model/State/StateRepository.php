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
use ReflectionObject;

use function assert;
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
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * @param RepositoryManager $repositoryManager Contao model repository manager.
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Find last workflow state of an entity.
     *
     * @param EntityId $entityId The entity id.
     *
     * @return State[]|iterable
     */
    public function find(EntityId $entityId): iterable
    {
        $repository = $this->repositoryManager->getRepository(StateModel::class);
        $collection = $repository->findBy(
            ['.entityId=?'],
            [(string) $entityId],
            ['order' => 'tl_workflow_state.reachedAt']
        );

        $states = [];
        foreach ($collection ?: [] as $model) {
            assert($model instanceof StateModel);
            $states[] = $this->createState($model);
        }

        return $states;
    }

    /**
     * Add a new state.
     *
     * @param State $state The new state.
     */
    public function add(State $state): void
    {
        // state is immutable. only store new states.
        if ($state->getStateId()) {
            return;
        }

        $model = $this->convertStateToModel($state);
        $this->repositoryManager->getRepository(StateModel::class)->save($model);

        // dynamically add state id.
        $reflector = new ReflectionObject($state);
        $property  = $reflector->getProperty('stateId');
        $property->setAccessible(true);
        $property->setValue($state, $model->id);
    }

    /**
     * Convert state object to model representation.
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
            $model->targetWorkflowName
        );
    }

    /**
     * Serialize data.
     *
     * @param mixed $data The data being serialized.
     */
    private function serialize($data): string
    {
        $data = $this->prepareSerialize($data);

        return json_encode($data);
    }

    /**
     * Prepare serializsation.
     *
     * @param mixed $data The data being serialized.
     *
     * @return mixed
     */
    private function prepareSerialize($data)
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
