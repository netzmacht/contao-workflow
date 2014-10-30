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
use Netzmacht\Contao\Workflow\Model\State;

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
    public function add(State $state)
    {
        var_dump($state);
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
            $model->workflowName,
            $model->transitionName,
            $model->stepName,
            (bool)$model->success,
            deserialize($model->data, true),
            $reachedAt,
            deserialize($model->errors, true)
        );

        return $state;
    }
}
