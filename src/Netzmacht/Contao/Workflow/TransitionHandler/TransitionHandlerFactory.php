<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\TransitionHandler;

use ContaoCommunityAlliance\DcGeneral\Contao\InputProvider;
use Netzmacht\Contao\Workflow\Entity\EntityManager;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Item;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\Transaction\TransactionHandler;
use Netzmacht\Contao\Workflow\TransitionHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

class TransitionHandlerFactory
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * @var InputProvider
     */
    private $inputProvider;

    /**
     * @var bool
     */
    private $useEventDispatching;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param EntityManager      $entityManager
     * @param TransactionHandler $transactionHandler
     * @param InputProvider      $inputProvider
     * @param EventDispatcher    $eventDispatcher
     */
    function __construct(
        EntityManager $entityManager, 
        TransactionHandler $transactionHandler,
        InputProvider $inputProvider,
        EventDispatcher $eventDispatcher
    ) {
        $this->entityManager      = $entityManager;
        $this->transactionHandler = $transactionHandler;
        $this->inputProvider      = $inputProvider;
        $this->eventDispatcher    = $eventDispatcher;
    }

    /**
     * Create a new transition handler.
     *
     * @param Item            $item
     * @param Workflow        $workflow
     * @param string          $transitionName
     * @param string          $providerName
     * @param StateRepository $stateRepository
     *
     * @return TransitionHandler
     */
    public function createTransitionHandler(
        Item $item,
        Workflow $workflow,
        $transitionName,
        $providerName,
        StateRepository $stateRepository
    ) {
        $handler = $this->createSimpleTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $providerName,
            $stateRepository
        );

        if ($this->useEventDispatching) {
            $handler = new EventDispatchingTransitionHandler($handler, $this->eventDispatcher);
        }

        return $handler;
    }

    /**
     * @param $enable
     */
    public function useEventDispatching($enable)
    {
        $this->useEventDispatching = $enable;
    }

    /**
     * @param Item            $item
     * @param Workflow        $workflow
     * @param string          $transitionName
     * @param string          $providerName
     * @param StateRepository $stateRepository
     *
     * @return SimpleTransitionHandler
     */
    private function createSimpleTransitionHandler(
        Item $item,
        Workflow $workflow,
        $transitionName,
        $providerName,
        StateRepository $stateRepository
    ) {
        return new SimpleTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $this->entityManager->getRepository($providerName),
            $stateRepository,
            $this->transactionHandler,
            new Context($this->inputProvider)
        );
    }
}
