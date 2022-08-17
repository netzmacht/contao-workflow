<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;

use function array_merge;
use function assert;
use function count;
use function end;
use function explode;
use function implode;
use function in_array;
use function is_string;
use function sprintf;

/**
 * Helper trait to get the entity properties for a workflow
 */
trait EntityPropertiesTrait
{
    /**
     * Get the definition for a data container.
     *
     * @param string $dataContainerName The name of the data container.
     */
    abstract protected function getDefinition(string $dataContainerName = ''): Definition;

    /**
     * Get the entity properties for a workflow.
     *
     * @param WorkflowModel $workflow The workflow.
     *
     * @return array<string,array<string,string>>
     */
    protected function getEntityPropertiesForWorkflow(WorkflowModel $workflow): array
    {
        $options = [];

        foreach ($this->getRelations($workflow->providerName) as $related) {
            if ($related[1]) {
                $lastColumn = end($related[1]);
                $value      = implode('.', $related[1]) . '.' . $related[2];
                $group      = sprintf(
                    '%s: %s [%s]',
                    $this->getFormatter($related[3])->formatFieldLabel($lastColumn),
                    $related[0],
                    implode('.', $related[1])
                );
            } else {
                $value = $related[2];
                $group = $related[0];
            }

            $options[$group][$value] = sprintf(
                '%s [%s.%s]',
                $this->getFormatter($related[0])->formatFieldLabel($related[2]),
                $related[0],
                $related[2]
            );
        }

        return $options;
    }

    /**
     * Get relations for the current table.
     *
     * Each iterable item contains an array of 4 items:
     *  - current table
     *  - prefix as array
     *  - current field
     *  - parent table if available
     *
     * @param string       $currentTable The current table.
     * @param int          $depth        Limit the depth. Detph 1 means it checks the first related level.
     * @param string       $parentTable  The parent table.
     * @param list<string> $prefix       The prefix path as array.
     * @param list<string> $knownTables  Cache of known tables. Required to avoid recursion.
     *
     * @return iterable<array{0:string,1:list<string>,2:string,3:string}>
     */
    private function getRelations(
        string $currentTable,
        int $depth = 1,
        string $parentTable = '',
        array $prefix = [],
        array &$knownTables = []
    ): iterable {
        if (in_array($currentTable, $knownTables, true)) {
            return [];
        }

        $definition    = $this->getDefinition($currentTable);
        $knownTables[] = $currentTable;

        foreach ($definition->get(['fields']) as $fieldName => $fieldConfiguration) {
            assert(is_string($fieldName));

            if (! isset($fieldConfiguration['relation'])) {
                yield [$currentTable, $prefix, $fieldName, $parentTable];

                continue;
            }

            if (
                ! isset($fieldConfiguration['relation']['type'])
                || ! in_array($fieldConfiguration['relation']['type'], ['hasOne', 'belongsTo'])
            ) {
                // Skip field, as it has an 1:m relation which is not supported.
                continue;
            }

            if (isset($fieldConfiguration['relation']['table'])) {
                $relatedTable = $fieldConfiguration['relation']['table'];
            } elseif (isset($fieldConfiguration['foreignKey'])) {
                $relatedTable = explode('.', $fieldConfiguration['foreignKey'], 2)[0];
            } else {
                // Can't determine related table, so skip field.
                continue;
            }

            if (count($prefix) === $depth) {
                continue;
            }

            yield from $this->getRelations(
                $relatedTable,
                $depth,
                $currentTable,
                array_merge($prefix, [$fieldName]),
                $knownTables
            );
        }
    }
}
