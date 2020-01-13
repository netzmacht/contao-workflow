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
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;

/**
 * Class used for tl_workflow_step callbacks.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Dca
 */
final class StepCallbackListener extends AbstractListener
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
     * Transition constructor.
     *
     * @param DcaManager        $dcaManager        Data container manager.
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function __construct(
        DcaManager $dcaManager,
        RepositoryManager $repositoryManager
    ) {
        parent::__construct($dcaManager);

        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Get steps which can be a target.
     *
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getWorkflows($dataContainer): array
    {
        $allWorkflows      = [];

        $allWorkflows[''] = '';
        $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $collection = $repository->findAll();

        if ($collection) {
            while ($collection->next()) {
                $allWorkflows[$collection->id] = $collection->label;
            }
        }

        return $allWorkflows;
    }
}
