<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Migration;

use Contao\CoreBundle\Doctrine\Schema\SchemaProvider;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Override;

/**
 * Migrates action relations to reference actions
 */
final class TransitionActionsMigration extends AbstractMigration
{
    /**
     * @param Connection     $connection     Database connection.
     * @param SchemaProvider $schemaProvider Dca schema provider.
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly SchemaProvider $schemaProvider,
    ) {
    }

    #[Override]
    public function shouldRun(): bool
    {
        return $this->connection
            ->createSchemaManager()
            ->tablesExist(['tl_workflow_action', 'tl_workflow_transition_action']);
    }

    #[Override]
    public function run(): MigrationResult
    {
        $schemaManager = $this->connection->createSchemaManager();
        $fromTable     = $schemaManager->introspectSchema()->getTable('tl_workflow_action');
        /** @psalm-suppress DeprecatedMethod - Deprecated in Contao 4.11 */
        $toTable    = $this->schemaProvider->createSchema()->getTable('tl_workflow_action');
        $fromSchema = new Schema([$fromTable]);
        $toSchema   = new Schema([$toTable]);
        $diff       = $schemaManager->createComparator()->compareSchemas($fromSchema, $toSchema);
        $statements = $this->connection->getDatabasePlatform()->getAlterSchemaSQL($diff);

        foreach ($statements as $sql) {
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

        return $this->$this->createResult(true);
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
            ],
        );
    }
}
