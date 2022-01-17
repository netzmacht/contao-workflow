<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\DriverResultStatement;
use Doctrine\DBAL\ForwardCompatibility\DriverStatement;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\Workflow\Flow\Security\Permission;

use function array_keys;
use function assert;
use function current;
use function is_int;
use function next;
use function serialize;

/** @Callback(table="tl_workflow", target="config.oncopy") */
final class CopyWorkflowCallbackListener
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /** @param int|string $insertId */
    public function __invoke($insertId, DataContainer $dataContainer): void
    {
        $transitionMapping = $this->generateTransitionMapping((int) $insertId, (int) $dataContainer->id);
        $stepMapping       = $this->generateStepMapping((int) $insertId, (int) $dataContainer->id);

        $this->fixProcess((int) $insertId, $transitionMapping, $stepMapping);
        $this->fixTargetSteps((int) $insertId, $stepMapping);
        $this->fixPermissions((int) $insertId);
        $this->fixReferenceActions((int) $insertId, (int) $dataContainer->id);
        $this->copyConditionalTransitions($transitionMapping);
    }

    /** @return array<string,string> */
    private function generateTransitionMapping(int $newWorkflowId, int $oldWorkflowId): array
    {
        $oldTransitionIds = $this->fetchTransitionIds($oldWorkflowId);
        $newTransitionIds = $this->fetchTransitionIds($newWorkflowId);
        $mapping          = [];

        foreach ($oldTransitionIds as $oldTransitionId) {
            $mapping[$oldTransitionId] = current($newTransitionIds);
            next($newTransitionIds);
        }

        return $mapping;
    }

    /** @return array<string,string> */
    private function generateStepMapping(int $newWorkflowId, int $oldWorkflowId): array
    {
        $oldStepIds = $this->fetchStepIds($oldWorkflowId);
        $newStepIds = $this->fetchStepIds($newWorkflowId);
        $mapping    = [];

        foreach ($oldStepIds as $oldStepId) {
            $mapping[$oldStepId] = current($newStepIds);
            next($newStepIds);
        }

        return $mapping;
    }

    /** @return list<string> */
    private function fetchTransitionIds(int $workflowId): array
    {
        return $this->connection
            ->executeQuery(
                'SELECT id FROM tl_workflow_transition WHERE pid = :workflow ORDER BY sorting',
                ['workflow' => $workflowId]
            )
            ->fetchFirstColumn();
    }

    /** @return list<string> */
    private function fetchStepIds(int $workflowId): array
    {
        return $this->connection
            ->executeQuery(
                'SELECT id FROM tl_workflow_step WHERE pid = :workflow ORDER BY id',
                ['workflow' => $workflowId]
            )
            ->fetchFirstColumn();
    }

    /**
     * @param array<string,string> $stepMapping
     * @param array<string,string> $transitionMapping
     */
    private function fixProcess(int $workflowId, array $transitionMapping, array $stepMapping): void
    {
        $process = $this->connection
            ->executeQuery(
                'SELECT process FROM tl_workflow WHERE id = :workflow',
                ['workflow' => $workflowId]
            )
            ->fetchOne();

        $process = StringUtil::deserialize($process, true);

        foreach ($process as $index => $config) {
            if ($config['step'] !== 'start') {
                $process[$index]['step'] = $stepMapping[$config['step']] ?? null;
            }

            $process[$index]['transition'] = $transitionMapping[$config['transition']] ?? null;
        }

        $this->connection->update('tl_workflow', ['process' => serialize($process)], ['id' => $workflowId]);
    }

    /** @param array<string,string> $stepMapping */
    private function fixTargetSteps(int $workflowId, array $stepMapping): void
    {
        foreach ($stepMapping as $old => $new) {
            $this->connection->update(
                'tl_workflow_transition',
                ['stepTo' => $new],
                ['pid' => $workflowId, 'stepTo' => $old]
            );
        }
    }

    private function fixPermissions(int $newWorkflowId): void
    {
        $this->fixPermissionsForTable('tl_workflow_step', 'permission', $newWorkflowId);
        $this->fixPermissionsForTable('tl_workflow_transition', 'permission', $newWorkflowId);

        $query = <<<'SQL'
SELECT a.id, a.assign_user_permission 
  FROM tl_workflow_action a
LEFT JOIN tl_workflow_transition t ON t.id = a.pid AND a.ptable = :transitionTable
WHERE a.assign_user_permission != ''
 AND ((a.ptable = :workflowTable AND a.pid = :workflowId) OR (t.id IS NOT NULL))
SQL;

        $result = $this->connection->executeQuery(
            $query,
            [
                'workflowTable'   => WorkflowModel::getTable(),
                'transitionTable' => TransitionModel::getTable(),
                'workflowId'      => $newWorkflowId,
            ]
        );
        $this->fixPermissionsForResult($result, 'tl_workflow_action', 'assign_user_permission', $newWorkflowId);
    }

    private function fixPermissionsForTable(string $table, string $column, int $newWorkflowId): void
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id,' . $column)
            ->from($table)
            ->where('pid = :workflow')
            ->andWhere($column . ' != \'\'')
            ->setParameter('workflow', $newWorkflowId)
            ->execute();

        assert(! is_int($result));

        $this->fixPermissionsForResult($result, $table, $column, $newWorkflowId);
    }

    /** @param DriverStatement|DriverResultStatement $result */
    private function fixPermissionsForResult($result, string $table, string $column, int $newWorkflowId): void
    {
        while ($row = $result->fetchAssociative()) {
            $permission = Permission::fromString($row[$column]);
            $permission = Permission::forWorkflowName('workflow_' . $newWorkflowId, $permission->getPermissionId());

            $this->connection->update($table, [$column => (string) $permission], ['id' => $row['id']]);
        }
    }

    /** @param array<string,string> $transitionMapping */
    private function copyConditionalTransitions(array $transitionMapping): void
    {
        $result = $this->connection->executeQuery(
            'SELECT * FROM tl_workflow_transition_conditional_transition WHERE pid IN (:transitions)',
            ['transitions' => array_keys($transitionMapping)],
            ['transitions' => Connection::PARAM_STR_ARRAY]
        );

        while ($row = $result->fetchAssociative()) {
            unset($row['id']);
            $row['pid'] = $transitionMapping[$row['pid']];
            $row['tid'] = $transitionMapping[$row['tid']];

            $this->connection->insert('tl_workflow_transition_conditional_transition', $row);
        }
    }

    /**
     * Requires https://github.com/contao/contao/pull/3939
     */
    private function fixReferenceActions(int $newWorkflowId, int $oldWorkflowId): void
    {
        $query = <<<'sql'
    SELECT a.id, a.reference 
      FROM tl_workflow_action a
INNER JOIN tl_workflow_transition t ON t.id = a.pid AND a.ptable = :transitionTable
WHERE a.type = :referenceType AND t.pid = :workflowId
sql;

        $mapping = $this->generateWorkflowActionsMapping($newWorkflowId, $oldWorkflowId);
        $result  = $this->connection->executeQuery(
            $query,
            [
                'transitionTable' => TransitionModel::getTable(),
                'referenceType'   => 'reference',
                'workflowId'      => $newWorkflowId,
            ]
        );

        while ($row = $result->fetchAssociative()) {
            $this->connection->update(
                'tl_workflow_action',
                [
                    'reference' => $mapping[$row['reference']] ?? 0,
                ],
                [
                    'id' => $row['id'],
                ]
            );
        }
    }

    /** @return list<string> */
    private function fetchWorkflowActionIds(int $workflowId): array
    {
        return $this->connection
            ->executeQuery(
                'SELECT id FROM tl_workflow_action WHERE ptable=:workflowTable AND pid = :workflowId ORDER BY sorting',
                [
                    'workflowTable' => WorkflowModel::getTable(),
                    'workflowId'    => $workflowId,
                ]
            )
            ->fetchFirstColumn();
    }

    /** @return array<string,string> */
    private function generateWorkflowActionsMapping(int $newWorkflowId, int $oldWorkflowId): array
    {
        $oldActionIds = $this->fetchWorkflowActionIds($oldWorkflowId);
        $newActionIds = $this->fetchWorkflowActionIds($newWorkflowId);
        $mapping      = [];

        foreach ($oldActionIds as $oldActionId) {
            $current = current($newActionIds);
            if ($current === false) {
                break;
            }

            $mapping[$oldActionId] = $current;
            next($newActionIds);
        }

        return $mapping;
    }
}
