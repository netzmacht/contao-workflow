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
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\ContaoWorkflowBundle\Model\Action\ActionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Step\StepModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeRegistry;

/**
 * Class Transition used for tl_workflow_transition callbacks.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Dca
 */
class TransitionCallbackListener extends AbstractListener
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $name = 'tl_workflow_transition';

    /**
     * Type provider.
     *
     * @var WorkflowTypeRegistry
     */
    private $typeRegistry;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Transition constructor.
     *
     * @param WorkflowTypeRegistry $typeRegistry      Workflow type registry.
     * @param DcaManager           $dcaManager        Data container manager.
     * @param RepositoryManager    $repositoryManager Repository manager.
     */
    public function __construct(
        WorkflowTypeRegistry $typeRegistry,
        DcaManager $dcaManager,
        RepositoryManager $repositoryManager
    ) {
        parent::__construct($dcaManager);

        $this->typeRegistry      = $typeRegistry;
        $this->repositoryManager = $repositoryManager;
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
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getEntityProperties($dataContainer): array
    {
        if ($dataContainer->activeRecord) {
            $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
            $workflow   = $repository->find((int) $dataContainer->activeRecord->pid);

            if ($workflow) {
                $schemaManager = $this->repositoryManager->getConnection()->getSchemaManager();
                $fields        = array_keys($schemaManager->listTableColumns($workflow->providerName));
                $options       = [];
                $formatter     = $this->getFormatter((string) $workflow->providerName);

                foreach ($fields as $field) {
                    $options[$field] = sprintf(
                        '%s [%s]',
                        $formatter->formatFieldLabel($field),
                        $field
                    );
                }

                return $options;
            }
        }

        return [];
    }

    /**
     * Get all actions.
     *
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getActions($dataContainer)
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
     * @throws \Doctrine\DBAL\DBALException If any dbal error occurs.
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
     * @throws \Doctrine\DBAL\DBALException
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
}
