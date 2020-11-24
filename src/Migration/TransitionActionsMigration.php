<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Migration;

use Doctrine\DBAL\Connection;
use PDO;

/**
 * Migrates action relations to reference actions
 */
final class TransitionActionsMigration
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * TransitionActionsMigration constructor.
     *
     * @param Connection $connection Database connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Invoke the migration.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $schemaManager = $this->connection->getSchemaManager();
        if ($schemaManager === null) {
            return;
        }

        if (! $schemaManager->tablesExist(['tl_workflow_action', 'tl_workflow_transition_action'])) {
            return;
        }

        $sql = <<<'SQL'
    SELECT r.*, a.active 
      FROM tl_workflow_transition_action r
INNER JOIN tl_workflow_action a ON a.id = r.aid 
 ORDER BY sorting
SQL;

        $statement = $this->connection->executeQuery($sql);
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $this->migrateRelation($row);
        }

        $schemaManager->dropTable('tl_workflow_transition_action');
    }

    /**
     * Migrate a transition action relation.
     *
     * @param array $row The relation dataset.
     *
     * @return void
     */
    private function migrateRelation(array $row): void
    {
        $this->connection->insert(
            'tl_workflow_action',
            [
                'type'      => 'reference',
                'tstamp'    => $row['tstamp'],
                'ptable'    => 'tl_workflow_transition',
                'pid'       => $row['tid'],
                'sorting'   => $row['sorting'],
                'reference' => $row['aid'],
                'active'    => $row['active']
            ]
        );
    }
}
