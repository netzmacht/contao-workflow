<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\ContaoWorkflowBundle\Model\Action\ActionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Step\StepModel;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\Translation\TranslatorInterface;
use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_pop;
use function explode;
use function implode;
use function in_array;
use function sprintf;
use function time;

/**
 * Class Transition used for tl_workflow_transition callbacks.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Dca
 */
final class TransitionCallbackListener extends AbstractListener
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $name = 'tl_workflow_transition';

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Configuration of available transition types.
     *
     * @var array<array>
     */
    private $transitionTypes;

    /**
     * Workflow manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Transition constructor.
     *
     * @param DcaManager          $dcaManager        Data container manager.
     * @param RepositoryManager   $repositoryManager Repository manager.
     * @param WorkflowManager     $workflowManager   Workflow manager.
     * @param TranslatorInterface $translator        Translator.
     * @param array<array>        $transitionTypes   Configuration of available transition types.
     */
    public function __construct(
        DcaManager $dcaManager,
        RepositoryManager $repositoryManager,
        WorkflowManager $workflowManager,
        TranslatorInterface $translator,
        array $transitionTypes
    ) {
        parent::__construct($dcaManager);

        $this->repositoryManager = $repositoryManager;
        $this->transitionTypes   = $transitionTypes;
        $this->workflowManager   = $workflowManager;
        $this->translator        = $translator;
    }

    /**
     * Get available transition types.
     *
     * @return array<string>
     */
    public function getTypes(): array
    {
        return array_keys($this->transitionTypes);
    }

    /**
     * Generate a row view.
     *
     * @param array $row Current data row.
     *
     * @return string
     */
    public function generateRow(array $row): string
    {
        return sprintf(
            '%s <span class="tl_gray">[%s]</span>',
            $row['label'],
            $this->getFormatter()->formatValue('type', $row['type'])
        );
    }

    /**
     * Get steps which can be a target.
     *
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getStepsTo($dataContainer): array
    {
        $steps      = [];
        $repository = $this->repositoryManager->getRepository(StepModel::class);
        $collection = $repository->findBy(['.pid=?'], [$dataContainer->activeRecord->pid], ['order' => '.label']);

        if ($collection) {
            while ($collection->next()) {
                $steps[$collection->id] = $collection->label;

                if ($collection->final) {
                    $steps[$collection->id] .= ' [final]';
                }
            }
        }

        return $steps;
    }

    /**
     * Get entity properties.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getEntityProperties($dataContainer): array
    {
        if (! $dataContainer->activeRecord) {
            return [];
        }

        $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $workflow   = $repository->find((int) $dataContainer->activeRecord->pid);

        if (!$workflow instanceof WorkflowModel) {
            return [];
        }

        $options = [];

        foreach ($this->getRelations($workflow->providerName) as $related) {
            if ($related[1]) {
                $lastColumn = end($related[1]);
                $group      = sprintf(
                    '%s: %s [%s]',
                    $this->getFormatter($related[3])->formatFieldLabel($lastColumn),
                    $related[0],
                    implode('.', $related[1])
                );
            } else {
                $group = $related[0];
            }

            $options[$group][implode('.', $related[1]) . '.' . $related[2]] = sprintf(
                '%s [%s.%s]',
                $this->getFormatter($related[0])->formatFieldLabel($related[2]),
                $related[0],
                $related[2]
            );
        }

        return $options;
    }

    /**
     * Get all actions.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getActions($dataContainer): array
    {
        if ($dataContainer->activeRecord) {
            $repository = $this->repositoryManager->getRepository(ActionModel::class);
            $collection = $repository->findBy(['.pid=?'], [$dataContainer->activeRecord->pid], ['.label']);

            return OptionsBuilder::fromCollection($collection, 'label')->getOptions();
        }

        return [];
    }

    /**
     * Load related actions.
     *
     * @param mixed          $value         The actual value.
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @throws DBALException If any dbal error occurs.
     */
    public function loadRelatedActions($value, $dataContainer)
    {
        $statement = $this->repositoryManager->getConnection()
            ->prepare('SELECT aid FROM tl_workflow_transition_action WHERE tid=:tid ORDER BY sorting');

        if ($statement->execute(['tid' => $dataContainer->id])) {
            return $statement->fetchAll(\PDO::FETCH_COLUMN, 0);
        }

        return [];
    }

    /**
     * Save all related actions.
     *
     * @param mixed         $value         The value.
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return null
     * @throws DBALException When an database related error occurs.
     */
    public function saveRelatedActions($value, $dataContainer)
    {
        $connection = $this->repositoryManager->getConnection();
        $new        = array_filter(StringUtil::deserialize($value, true));
        $values     = [];
        $statement  = $connection->prepare(
            'SELECT * FROM tl_workflow_transition_action WHERE tid=:tid order BY sorting'
        );

        $statement->bindValue('tid', $dataContainer->id);
        $statement->execute();

        while ($row = $statement->fetch()) {
            $values[$row['aid']] = $row;
        }

        $sorting = 0;

        foreach ($new as $actionId) {
            if (!isset($values[$actionId])) {
                $data = [
                    'tstamp'  => time(),
                    'aid'     => $actionId,
                    'tid'     => $dataContainer->id,
                    'sorting' => $sorting,
                ];

                $connection->insert('tl_workflow_transition_action', $data);
                $sorting += 128;
            } else {
                if ($values[$actionId]['sorting'] <= ($sorting - 128)
                    || $values[$actionId]['sorting'] >= ($sorting + 128)
                ) {
                    $connection->update(
                        'tl_workflow_transition_action',
                        ['tstamp' => time(), 'sorting' => $sorting],
                        ['id' => $values[$actionId]['id']]
                    );
                }

                $sorting += 128;
                unset($values[$actionId]);
            }
        }

        $ids = array_map(
            function ($item) {
                return $item['id'];
            },
            $values
        );

        if ($ids) {
            $connection->executeUpdate(
                'DELETE FROM tl_workflow_transition_action WHERE id IN(?)',
                [$ids],
                [Connection::PARAM_INT_ARRAY]
            );
        }

        return null;
    }


    /**
     * Get all conditional transitions.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getConditionalTransitions($dataContainer): array
    {
        $repository = $this->repositoryManager->getRepository(TransitionModel::class);

        if ($dataContainer->activeRecord) {
            $collection = $repository->findBy(['.id != ?'], [$dataContainer->activeRecord->id]);
        } else {
            $collection = $repository->findAll();
        }

        return OptionsBuilder::fromCollection($collection, 'label')->getOptions();
    }

    /**
     * Load conditional transitions.
     *
     * @param mixed          $value         The actual value.
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @throws DBALException If any dbal error occurs.
     */
    public function loadConditionalTransitions($value, $dataContainer)
    {
        $statement = $this->repositoryManager
            ->getConnection()
            ->prepare('SELECT tid FROM tl_workflow_transition_conditional_transition WHERE pid=:pid ORDER BY sorting');

        if ($statement->execute(['pid' => $dataContainer->id])) {
            return $statement->fetchAll(\PDO::FETCH_COLUMN, 0);
        }

        return [];
    }

    /**
     * Save selected conditional transitions.
     *
     * @param mixed         $value         The value.
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return null
     * @throws DBALException When an database related error occurs.
     */
    public function saveConditionalTransitions($value, $dataContainer)
    {
        $connection = $this->repositoryManager->getConnection();
        $new        = array_filter(StringUtil::deserialize($value, true));
        $values     = [];
        $statement  = $connection->prepare(
            'SELECT * FROM tl_workflow_transition_conditional_transition WHERE pid=:pid order BY sorting'
        );

        $statement->bindValue('pid', $dataContainer->id);
        $statement->execute();

        while ($row = $statement->fetch()) {
            $values[$row['tid']] = $row;
        }

        $sorting = 0;

        foreach ($new as $conditionalTransitionId) {
            if (!isset($values[$conditionalTransitionId])) {
                $data = [
                    'tstamp'  => time(),
                    'pid'     => $dataContainer->id,
                    'tid'     => $conditionalTransitionId,
                    'sorting' => $sorting,
                ];

                $connection->insert('tl_workflow_transition_conditional_transition', $data);
                $sorting += 128;
            } else {
                if ($values[$conditionalTransitionId]['sorting'] <= ($sorting - 128)
                    || $values[$conditionalTransitionId]['sorting'] >= ($sorting + 128)
                ) {
                    $connection->update(
                        'tl_workflow_transition_conditional_transition',
                        ['tstamp' => time(), 'sorting' => $sorting],
                        ['id' => $values[$conditionalTransitionId]['id']]
                    );
                }

                $sorting += 128;
                unset($values[$conditionalTransitionId]);
            }
        }

        $ids = array_map(
            function ($item) {
                return $item['id'];
            },
            $values
        );

        if ($ids) {
            $connection->executeUpdate(
                'DELETE FROM tl_workflow_transition_conditional_transition WHERE id IN(?)',
                [$ids],
                [Connection::PARAM_INT_ARRAY]
            );
        }

        return null;
    }

    /**
     * Get all workflow options for the workflow transition.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getWorkflows(DataContainer $dataContainer): array
    {
        if (! $dataContainer->activeRecord) {
            $workflows = $this->workflowManager->getWorkflows();
        } else {
            $workflows     = [];
            $workflowModel = $this->repositoryManager
                ->getRepository(WorkflowModel::class)
                ->find((int) $dataContainer->activeRecord->pid);

            if ($workflowModel) {
                foreach ($this->workflowManager->getWorkflows() as $workflow) {
                    if ($workflow->getName() === 'workflow_' . $workflowModel->id
                        || $workflow->getProviderName() !== $workflowModel->providerName
                    ) {
                        continue;
                    }

                    $workflows[] = $workflow;
                }
            } else {
                $workflows = $this->workflowManager->getWorkflows();
            }
        }

        $options = [];

        foreach ($workflows as $workflow) {
            $options[$workflow->getName()] = $workflow->getLabel();
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
