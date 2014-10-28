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
        $result = $this
            ->prepareFindStatement($success)
            ->limit(1)
            ->execute($providerName, $entityId);

        if (!$result->numRows) {
            return null;
        }

        return $this->createState($result);
    }

    /**
     * {@inheritdoc}
     */
    public function add(State $state)
    {
    }

    /**
     * Prepare the find statement.
     *
     * @param bool $success Only load successful states.
     *
     * @return \Database\Statement
     */
    private function prepareFindStatement($success)
    {
        $query = 'SELECT * FROM tl_workflow_state WHERE providerName=? AND entityId=?';

        if ($success) {
            $query .= ' AND success=1';
        }

        $query .= ' ORDER BY reachedAt DESC';

        return $this->database->prepare($query);
    }

    /**
     * Create the state object.
     *
     * @param \Database\Result $result The database result.
     *
     * @return State
     */
    private function createState($result)
    {
        $reachedAt = new \DateTime();
        $reachedAt->setTimestamp($result->reachedAt);

        $state = new State(
            $result->transitionName,
            $result->stepName,
            (bool)$result->success,
            deserialize($result->data, true),
            $reachedAt,
            deserialize($result->errors, true)
        );

        return $state;
    }
}
