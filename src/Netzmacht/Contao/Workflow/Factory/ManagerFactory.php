<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Factory;

use ContaoCommunityAlliance\DcGeneral\Contao\InputProvider as ContaoInputProvider;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface as InputProvider;
use Netzmacht\Contao\Workflow\Factory\Event\CreateManagerEvent;
use Netzmacht\Contao\Workflow\Manager;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\Transaction\TransactionHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ManagerFactory creates the workflow manager.
 *
 * @package Netzmacht\Contao\Workflow\Factory
 */
class ManagerFactory implements EventSubscriberInterface
{
    /**
     * @var
     */
    private $stateRepository;

    /**
     * @var
     */
    private $transactionHandler;

    /**
     * @var
     */
    private $inputProvider;

    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateManagerEvent::NAME => 'handleCreateManager'
        );
    }

    /**
     * @param CreateManagerEvent $event
     *
     * @return Manager
     */
    public function handleCreateManager(CreateManagerEvent $event)
    {
        $container = $this->getContainer();
        $factory   = new self();

        $manager = $factory
            ->setStateRepository($container['workflow.state-repository'])
            ->setInputProvider(new ContaoInputProvider())
            ->setTransactionHandler($container['workflow.transaction-handler'])
            ->setRepositoryFactory($container['workflow.repository-factory'])
            ->create();

        $event->setManager($manager);
    }

    /**
     * Create the manager.
     *
     * @return Manager
     */
    public function create()
    {
        $manager = new Manager(
            $this->stateRepository,
            $this->repositoryFactory,
            $this->transactionHandler,
            $this->inputProvider
        );

        return $manager;
    }

    /**
     * @param StateRepository $stateRepository
     *
     * @return $this
     */
    public function setStateRepository(StateRepository $stateRepository)
    {
        $this->stateRepository = $stateRepository;

        return $this;
    }

    /**
     * @param InputProvider $inputProvider
     *
     * @return $this
     */
    public function setInputProvider(InputProvider $inputProvider)
    {
        $this->inputProvider = $inputProvider;

        return $this;
    }

    /**
     * Set the tranaction handler.
     *
     * @param TransactionHandler $transactionHandler Transaction handler.
     *
     * @return $this
     */
    public function setTransactionHandler(TransactionHandler $transactionHandler)
    {
        $this->transactionHandler = $transactionHandler;

        return $this;
    }

    /**
     * @param RepositoryFactory $repositoryFactory
     *
     * @return $this
     */
    public function setRepositoryFactory(RepositoryFactory $repositoryFactory)
    {
        $this->repositoryFactory = $repositoryFactory;

        return $this;
    }

    /**
     * Get the container.
     *
     * @return \Pimple
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getContainer()
    {
        return $GLOBALS['container'];
    }
}
