<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Data;

use Netzmacht\Workflow\Contao\Model\StateModel;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository as WorkflowStateRepository;
use Netzmacht\Workflow\Flow\State;

/**
 * Class StateRepository manages workflow states.
 *
 * @package Netzmacht\Workflow\Contao\Data
 */
class StateRepository implements WorkflowStateRepository
{
    /**
     * Find last worfklow state of an entity.
     *
     * @param EntityId $entityId The entity id.
     *
     * @return State[]
     */
    public function find(EntityId $entityId)
    {
        $collection = StateModel::findBy(
            array('tl_workflow_state.providerName=?', 'tl_workflow_state.entityId=?'),
            array($entityId->getProviderName(), $entityId->getIdentifier()),
            array('order' => 'tl_workflow_state.id DESC')
        );

        $states = array();

        if ($collection) {
            while ($collection->next()) {
                $states[] = $this->createState($collection->current());
            }
        }

        return $states;
    }

    /**
     * Add a new state.
     *
     * @param State $state The new state.
     *
     * @return void
     */
    public function add(State $state)
    {
        // state is immutable. only store new states.
        if (!$state->getStateId()) {
            return;
        }

        $model = $this->convertStateToModel($state);
        $model->save();

        // dynamically add state id.
        $reflector = new \ReflectionObject($state);
        $property  = $reflector->getProperty('stateId');
        $property->setAccessible(true);
        $property->setValue($state, $model->id);
    }

    /**
     * Convert state object to model representation.
     *
     * @param State $state The state being persisted.
     *
     * @return StateModel
     */
    private function convertStateToModel(State $state)
    {
        $model = new StateModel();

        $model->workflowName   = $state->getWorkflowName();
        $model->entityId       = (string) $state->getEntityId();
        $model->transitionName = $state->getTransitionName();
        $model->stepName       = $state->getStepName();
        $model->success        = $state->isSuccessful();
        $model->errors         = json_encode($state->getErrors());
        $model->data           = json_encode($state->getData());
        $model->reachedAt      = $state->getReachedAt()->getTimestamp();

        return $model;
    }

    /**
     * Create the state object.
     *
     * @param StateModel $model The state model.
     *
     * @return State
     */
    private function createState(StateModel $model)
    {
        $reachedAt = new \DateTime();
        $reachedAt->setTimestamp($model->reachedAt);

        $state = new State(
            EntityId::fromString($model->entityId),
            $model->workflowName,
            $model->transitionName,
            $model->stepName,
            (bool) $model->success,
            (array) json_decode($model->data, true),
            $reachedAt,
            (array) json_decode($model->errors, true),
            $model->id
        );

        return $state;
    }
}
