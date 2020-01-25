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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader\DatabaseDrivenWorkflowLoader;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionFactory;
use NotificationCenter\Model\Notification;

/**
 * Class Action is used for tl_workflow_action callbacks.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Dca
 */
final class ActionCallbackListener
{
    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Workflow definition loader.
     *
     * @var DatabaseDrivenWorkflowLoader
     */
    private $workflowLoader;

    /**
     * The action factory.
     *
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * Action constructor.
     *
     * @param RepositoryManager            $repositoryManager Repository manager.
     * @param DatabaseDrivenWorkflowLoader $workflowLoader    Database driven workflow loader.
     * @param ActionFactory                $actionFactory     The action factory.
     */
    public function __construct(
        RepositoryManager $repositoryManager,
        DatabaseDrivenWorkflowLoader $workflowLoader,
        ActionFactory $actionFactory
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->workflowLoader    = $workflowLoader;
        $this->actionFactory     = $actionFactory;
    }

    /**
     * Get all available types.
     *
     * @param DataContainer $dataContainer The data container.
     *
     * @return array
     */
    public function getTypes($dataContainer = null): array
    {
        if ($dataContainer === null || $dataContainer->activeRecord === null) {
            return $this->actionFactory->getTypeNames();
        }

        $workflow = $this->workflowLoader->loadWorkflowById((int) $dataContainer->activeRecord->pid);

        return $this->actionFactory->getSupportedTypeNamesCategorized($workflow);
    }

    /**
     * Get all notifications as options.
     *
     * @return array
     */
    public function notificationOptions(): array
    {
        $notifications = $this->repositoryManager
            ->getRepository(Notification::class)
            ->findBy(['.type=?'], ['workflow_transition'], ['order' => '.title']);

        return OptionsBuilder::fromCollection(
            $notifications,
            function (array $row) {
                return sprintf('%s [ID %s]', $row['title'], $row['id']);
            }
        )->getOptions();
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
        if ($dataContainer->activeRecord) {
            $repository = $this->repositoryManager->getRepository(TransitionModel::class);
            $collection = $repository->findAll();

            return OptionsBuilder::fromCollection($collection, 'label')->getOptions();
        }

        return [];
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
    public function loadConditionalTransitions($value, $dataContainer) {
        $statement = $this->repositoryManager->getConnection()
            ->prepare('SELECT tid FROM tl_workflow_action_conditionaltransition WHERE aid=:aid ORDER BY sorting');

        if ($statement->execute(['aid' => $dataContainer->id])) {
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
            'SELECT * FROM tl_workflow_action_conditionaltransition WHERE aid=:aid order BY sorting'
        );

        $statement->bindValue('aid', $dataContainer->id);
        $statement->execute();

        while ($row = $statement->fetch()) {
            $values[$row['tid']] = $row;
        }

        $sorting = 0;

        foreach ($new as $conditionalTransitionId) {
            if (!isset($values[$conditionalTransitionId])) {
                $data = [
                    'tstamp'  => time(),
                    'aid'     => $dataContainer->id,
                    'tid'     => $conditionalTransitionId,
                    'sorting' => $sorting,
                ];

                $connection->insert('tl_workflow_action_conditionaltransition', $data);
                $sorting += 128;
            } else {
                if ($values[$conditionalTransitionId]['sorting'] <= ($sorting - 128)
                    || $values[$conditionalTransitionId]['sorting'] >= ($sorting + 128)
                ) {
                    $connection->update(
                        'tl_workflow_action_conditionaltransition',
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
                'DELETE FROM tl_workflow_action_conditionaltransition WHERE id IN(?)',
                [$ids],
                [Connection::PARAM_INT_ARRAY]
            );
        }

        return null;
    }
}
