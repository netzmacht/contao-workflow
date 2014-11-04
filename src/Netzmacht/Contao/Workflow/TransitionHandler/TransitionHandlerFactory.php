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
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Workflow\Handler\AbstractTransitionHandler;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class TransitionHandlerFactory is responsible for creating the transition handler.
 *
 * @package Netzmacht\Contao\Workflow\TransitionHandler
 */
class TransitionHandlerFactory
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * The transaction handler.
     *
     * @var \Netzmacht\Workflow\Transaction\TransactionHandler
     */
    private $transactionHandler;

    /**
     * Consider if the event dispatching transaction handler should be used.
     *
     * @var bool
     */
    private $useEventDispatching;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Construct.
     *
     * @param EntityManager      $entityManager      The entity manager.
     * @param \Netzmacht\Workflow\Transaction\TransactionHandler $transactionHandler The transaction handler.
     * @param EventDispatcher    $eventDispatcher    THe event dispatcher.
     */
    public function __construct(
        EntityManager $entityManager,
        TransactionHandler $transactionHandler,
        EventDispatcher $eventDispatcher
    ) {
        $this->entityManager      = $entityManager;
        $this->transactionHandler = $transactionHandler;
        $this->eventDispatcher    = $eventDispatcher;
    }

    /**
     * Create a new transition handler.
     *
     * @param \Netzmacht\Workflow\Flow\Item            $item            Current workflow item.
     * @param Workflow        $workflow        Current workflow.
     * @param string          $transitionName  Transition which shall be handled.
     * @param string          $providerName    The provider name.
     * @param StateRepository $stateRepository The state repository.
     *
     * @return \Netzmacht\Workflow\\Netzmacht\Workflow\Handler\TransitionHandler
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
     * Consider if events should be dispatched.
     *
     * @param bool $enable If true the transition handler dispatches events.
     *
     * @return $this
     */
    public function useEventDispatching($enable)
    {
        $this->useEventDispatching = $enable;

        return $this;
    }

    /**
     * Create the simple transition handler.
     *
     * @param \Netzmacht\Workflow\Flow\Item            $item            The workflow item.
     * @param Workflow        $workflow        The workflow.
     * @param string          $transitionName  The name of the current transition.
     * @param string          $providerName    The name name of current provider.
     * @param StateRepository $stateRepository The state repository.
     *
     * @return AbstractTransitionHandler
     */
    private function createSimpleTransitionHandler(
        Item $item,
        Workflow $workflow,
        $transitionName,
        $providerName,
        StateRepository $stateRepository
    ) {
        return new AbstractTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $this->entityManager->getRepository($providerName),
            $stateRepository,
            $this->transactionHandler,
            new Context()
        );
    }
}
