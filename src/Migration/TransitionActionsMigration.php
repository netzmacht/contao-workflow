<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Migration;

use Contao\CoreBundle\Doctrine\Schema\SchemaProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

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
     * Dca Schema provider.
     *
     * @var SchemaProvider
     */
    private $schemaProvider;

    /**
     * @param Connection        $connection     Database connection.
     * @param SchemaProvider $schemaProvider Dca schema provider.
     */
    public function __construct(Connection $connection, SchemaProvider $schemaProvider)
    {
        $this->connection     = $connection;
        $this->schemaProvider = $schemaProvider;
    }

    /**
     * Invoke the migration.
     */
    public function __invoke(): void
    {
        $schemaManager = $this->connection->getSchemaManager();
        if (! $schemaManager->tablesExist(['tl_workflow_action', 'tl_workflow_transition_action'])) {
            return;
        }

        $schemaManager = $this->connection->getSchemaManager();
        $fromTable     = $schemaManager->createSchema()->getTable('tl_workflow_action');
        /** @psalm-suppress DeprecatedMethod - Deprecated in Contao 4.11 */
        $toTable    = $this->schemaProvider->createSchema()->getTable('tl_workflow_action');
        $fromSchema = new Schema([$fromTable]);
        $toSchema   = new Schema([$toTable]);

        foreach ($fromSchema->getMigrateToSql($toSchema, $this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->executeStatement($sql);
        }

        $sql = <<<'SQL'
    SELECT r.*, a.active 
      FROM tl_workflow_transition_action r
INNER JOIN tl_workflow_action a ON a.id = r.aid 
 ORDER BY sorting
SQL;

        $statement = $this->connection->executeQuery($sql);
        while ($row = $statement->fetchAssociative()) {
            $this->migrateRelation($row);
        }

        $this->connection->update('tl_workflow_action', ['ptable' => 'tl_workflow'], ['ptable' => '']);

        $schemaManager->dropTable('tl_workflow_transition_action');
    }

    /**
     * Migrate a transition action relation.
     *
     * @param array<string,mixed> $row The relation dataset.
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
                'active'    => $row['active'],
            ]
        );
    }
}
