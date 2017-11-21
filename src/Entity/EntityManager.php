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

namespace Netzmacht\Contao\Workflow\Entity;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use Netzmacht\Contao\Workflow\Entity\ContaoModel\ContaoModelEntityRepository as ContaoEntityRepository;
use Netzmacht\Workflow\Data\EntityManager as WorkflowEntityManager;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Transaction\TransactionHandler;

/**
 * Class EntityManager is the entity manager implementation for Contao.
 *
 * It creates the repositories and handles the transaction as well.
 *
 * @package Netzmacht\Contao\Workflow\Data
 */
class EntityManager implements WorkflowEntityManager, TransactionHandler
{
    /**
     * Entity repositories.
     *
     * @var EntityRepository[]|array
     */
    private $repositories = [];

    /**
     * Repository manager.
     *
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * The database connection.
     *
     * @param RepositoryFactory $repositoryFactory
     */
    public function __construct(RepositoryFactory $repositoryFactory)
    {
        $this->repositoryFactory = $repositoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $providerName): EntityRepository
    {
        if (isset($this->repositories[$providerName])) {
            return $this->repositories[$providerName];
        }

        $this->repositories[$providerName] = $this->repositoryFactory->create($providerName);

        return $this->repositories[$providerName];
    }

    /**
     * {@inheritdoc}
     */
    public function begin(): void
    {
        $this->repositoryManager->getConnection()->beginTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {
        $this->repositoryManager->getConnection()->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(): void
    {
        $this->repositoryManager->getConnection()->rollBack();
    }
}
