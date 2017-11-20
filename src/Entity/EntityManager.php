<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Entity;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Workflow\Data\EntityManager as WorkflowEntityManager;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

/**
 * Class EntityManager is the entity manager implementation for Contao.
 *
 * It creates the repositories and handles the transaction as well.
 *
 * @package Netzmacht\Contao\Workflow\Data
 */
class EntityManager implements WorkflowEntityManager, TransactionHandler, EventSubscriber
{
    /**
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Contao model adapter.
     *
     * @var Adapter|Model
     */
    private $modelAdapter;

    /**
     * Entity repositories.
     *
     * @var EntityRepository[]|array
     */
    private $repositories = [];

    /**
     * The database connection.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param Adapter|Model     $modelAdapter      Model adapter.
     */
    public function __construct(RepositoryManager $repositoryManager, $modelAdapter)
    {
        $this->repositoryManager = $repositoryManager;
        $this->modelAdapter      = $modelAdapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $providerName): EntityRepository
    {
        if (isset($this->repositories[$providerName])) {
            return $this->repositories[$providerName];
        }

        $modelClass = $this->modelAdapter->getClassFromTable($providerName);

        return $this->repositoryManager->getRepository($modelClass);
    }

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
        $this->connection->beginTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->connection->commitTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        $this->connection->rollbackTransaction();
    }
}
