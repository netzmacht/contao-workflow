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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use function array_merge;
use function count;
use function end;
use function explode;
use function implode;
use function in_array;
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
     *
     * @return Definition
     */
    abstract protected function getDefinition(string $dataContainerName = '') : Definition;

    /**
     * Get the entity properties for a workflow.
     *
     * @param WorkflowModel $workflow The workflow.
     *
     * @psalm-return array<string,string>
     * @return       array
     */
    protected function getEntityPropertiesForWorkflow(WorkflowModel $workflow) : array
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
     * @param string $currentTable The current table.
     * @param int    $depth        Limit the depth. Detph 1 means it checks the first related level.
     * @param string $parentTable  The parent table.
     * @param array  $prefix       The prefix path as array.
     * @param array  $knownTables  Cache of known tables. Required to avoid recursion.
     *
     * @return iterable
     */
    private function getRelations(
        string $currentTable,
        int $depth = 1,
        string $parentTable = '',
        array $prefix = [],
        array &$knownTables = []
    ) : iterable {
        if (in_array($currentTable, $knownTables, true)) {
            return [];
        }

        $definition    = $this->getDefinition($currentTable);
        $knownTables[] = $currentTable;

        foreach ($definition->get(['fields']) as $fieldName => $fieldConfiguration) {
            if (! isset($fieldConfiguration['relation'])) {
                yield [$currentTable, $prefix, $fieldName, $parentTable];

                continue;
            }

            if (!isset($fieldConfiguration['relation']['type'])
                || !in_array($fieldConfiguration['relation']['type'], ['hasOne', 'belongsTo'])
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
