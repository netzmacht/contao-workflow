<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Model;

use Netzmacht\Contao\Workflow\Contao\Model\StateModel;
use Netzmacht\Contao\Workflow\Contao\Model\StepModel;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\State;

/**
 * Class StateRepository stores workflow states.
 *
 * @package Netzmacht\Contao\Workflow\Model
 */
class ContaoStateRepository implements StateRepository
{
    /**
     * The database connection.
     *
     * @var \Database
     */
    private $database;

    /**
     * Construct.
     *
     * @param \Database $database Database connection.
     */
    public function __construct(\Database $database)
    {
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function find($providerName, $entityId, $success = true)
    {
        $collection = StateModel::findBy(
            array('tl_workflow_state.providerName=?', 'tl_workflow_state.entityId=?'),
            array($providerName, $entityId),
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
     * {@inheritdoc}
     */
    public function add(\Netzmacht\Workflow\Flow\State $state)
    {
        $model = new StateModel();

        $model->workflowName   = $state->getWorkflowName();
        $model->entityId       = $state->getEntityId();
        $model->providerName   = $state->getProviderName();
        $model->transitionName = $state->getTransitionName();
        $model->stepName       = $state->getStepName();
        $model->success        = $state->isSuccessful();
        $model->errors         = json_encode($state->getErrors());
        $model->data           = json_encode($state->getData());
        $model->reachedAt      = $state->getReachedAt()->getTimestamp();

        $model->save();

        $reflector = new \ReflectionObject($state);
        $property  = $reflector->getProperty('stateId');
        $property->setAccessible(true);
        $property->setValue($state, $model->id);
    }

    /**
     * Create the state object.
     *
     * @param StateModel $model The state model.
     *
     * @return \Netzmacht\Workflow\Flow\State
     */
    private function createState(StateModel $model)
    {
        $reachedAt = new \DateTime();
        $reachedAt->setTimestamp($model->reachedAt);

        $state = new \Netzmacht\Workflow\Flow\State(
            EntityId::fromScalars($model->providerName, $model->entityId),
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
