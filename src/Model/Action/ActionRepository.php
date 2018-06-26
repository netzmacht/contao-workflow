<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Action;

use Contao\Database\Result;
use Contao\Model\Collection;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;

/**
 * Class ActionRepository
 */
class ActionRepository extends ContaoRepository
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Construct.
     *
     * @param Connection $connection Database connection.
     */
    public function __construct(Connection $connection)
    {
        parent::__construct(ActionModel::class);

        $this->connection = $connection;
    }

    /**
     * Find by transition.
     *
     * @param int   $transitionId The transition id.
     * @param array $options      Query options.
     *
     * @return ActionModel[]|Collection|null
     */
    public function findByTransition(int $transitionId, array $options = ['order' => '.sorting'])
    {
        return $this->findBy(['.pid=?'], [$transitionId], $options);
    }

    /**
     * Find active by transition.
     *
     * @param int    $transitionId The transition id.
     * @param string $orderField   The order field.
     * @param string $direction    The direction.
     *
     * @return ActionModel[]|Collection|null
     */
    public function findActiveByTransition(int $transitionId, string $orderField = 'sorting', string $direction = 'ASC')
    {
        $builder = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from('tl_workflow_action', 'a')
            ->join('a', 'tl_workflow_transition_action', 't', 't.aid = a.id')
            ->where('t.tid = :tid')
            ->setParameter('tid', $transitionId)
            ->orderBy($orderField, $direction);

        $statement = $builder->execute();
        if ($statement->rowCount() === 0) {
            return null;
        }

        return Collection::createFromDbResult(
            new Result($statement, $builder->getSQL()),
            ActionModel::getTable()
        );
    }
}
