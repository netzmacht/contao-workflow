<?php

namespace spec\Netzmacht\Contao\Workflow;

use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Entity\EntityRepository;
use Netzmacht\Contao\Workflow\Factory\RepositoryFactory;
use Netzmacht\Contao\Workflow\Model\State;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Manager;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ManagerSpec
 * @package spec\Netzmacht\Contao\Workflow
 * @mixin Manager
 */
class ManagerSpec extends ObjectBehavior
{
    const ENTITY_PROVIDER_NAME = 'provider_name';

    const ENTITY_ID = 5;

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Manager');
    }

    function let(
        StateRepository $stateRepository,
        Workflow $workflow,
        RepositoryFactory $repositoryFactory,
        TransactionHandler $transactionHandler,
        InputProviderInterface $inputProvider
    ) {
        $this->beConstructedWith($stateRepository, $repositoryFactory, $transactionHandler, $inputProvider, array($workflow));
    }

    function it_gets_workflow(Workflow $workflow, Entity $entity)
    {
        $workflow->match($entity)->willReturn(true);

        $this->getWorkflow($entity)->shouldReturn($workflow);
    }

    function it_returns_false_if_no_workflow_found(Workflow $workflow, Entity $entity)
    {
        $workflow->match($entity)->willReturn(false);

        $this->getWorkflow($entity)->shouldReturn(false);
    }

    function it_knows_if_matching_workflow_exists(Workflow $workflow, Entity $entity)
    {
        $workflow->match($entity)->willReturn(true);
        $this->hasWorkflow($entity)->shouldReturn(true);
    }

    function it_adds_an_workflow(Workflow $anotherWorkflow)
    {
        $this->getWorkflows()->shouldNotContain($anotherWorkflow);
        $this->addWorkflow($anotherWorkflow)->shouldReturn($this);
        $this->getWorkflows()->shouldContain($anotherWorkflow);
    }

    function it_creates_the_transition_handler_for_a_start_transition(
        Workflow $workflow,
        Entity $entity,
        StateRepository $stateRepository,
        RepositoryFactory $repositoryFactory,
        EntityRepository $entityRepository
    ) {
        $entity->getState()->willReturn(null);
        $entity->getProviderName()->willReturn(static::ENTITY_PROVIDER_NAME);
        $entity->getId()->willReturn(static::ENTITY_ID);

        $repositoryFactory
            ->createEntityRepository(static::ENTITY_PROVIDER_NAME)
            ->willReturn($entityRepository);

        $stateRepository
            ->find(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID)
            ->willThrow('\Exception');

        $workflow->match($entity)->willReturn(true);
        $this->handle($entity)->shouldBeAnInstanceOf('Netzmacht\Contao\Workflow\TransitionHandler');
    }

    function it_creates_the_transition_handler_for_an_ongoing_transition(
        Workflow $workflow,
        Entity $entity,
        StateRepository $stateRepository,
        RepositoryFactory $repositoryFactory,
        EntityRepository $entityRepository,
        State $startState
    ) {
        $entity->getState()->willReturn(null);
        $entity->getProviderName()->willReturn(static::ENTITY_PROVIDER_NAME);
        $entity->getId()->willReturn(static::ENTITY_ID);

        $repositoryFactory
            ->createEntityRepository(static::ENTITY_PROVIDER_NAME)
            ->willReturn($entityRepository);

        $stateRepository
            ->find(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID)
            ->willReturn($startState);

        $workflow->match($entity)->willReturn(true);
        $this->handle($entity)->shouldBeAnInstanceOf('Netzmacht\Contao\Workflow\TransitionHandler');
    }

    function it_returns_false_if_no_matching_workflow_found(
        Workflow $workflow,
        Entity $entity
    ) {
        $workflow->match($entity)->willReturn(false);
        $this->handle($entity)->shouldReturn(false);
    }


}
