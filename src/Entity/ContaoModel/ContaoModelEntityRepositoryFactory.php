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

namespace Netzmacht\ContaoWorkflowBundle\Entity\ContaoModel;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Entity\RepositoryFactory;
use Netzmacht\ContaoWorkflowBundle\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityRepository;

/**
 * Class ContaoModelEntityRepositoryFactory
 *
 * @package Netzmacht\ContaoWorkflowBundle\Entity\ContaoModel
 */
class ContaoModelEntityRepositoryFactory implements RepositoryFactory
{
    /**
     * Contao model repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Model adapter.
     *
     * @var Adapter|Model
     */
    private $modelAdapter;

    /**
     * ContaoModelEntityRepositoryFactory constructor.
     *
     * @param RepositoryManager $repositoryManager
     * @param Adapter|Model     $modelAdapter
     */
    public function __construct(RepositoryManager $repositoryManager, $modelAdapter)
    {
        $this->repositoryManager = $repositoryManager;
        $this->modelAdapter      = $modelAdapter;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(string $providerName): bool
    {
        $modelClass = $this->modelAdapter->getClassFromTable($providerName);
        if (!$modelClass || !class_exists($modelClass) || !is_a($modelClass, Model::class, true)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When Entity type is not supported.
     */
    public function create(string $providerName): EntityRepository
    {
        $modelClass = $this->modelAdapter->getClassFromTable($providerName);
        if (!$modelClass) {
            throw UnsupportedEntity::withProviderName($providerName);
        }

        $repository = $this->repositoryManager->getRepository($modelClass);

        return new ContaoModelEntityRepository($repository);
    }
}
