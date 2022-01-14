<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

use function array_keys;
use function current;
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
}
