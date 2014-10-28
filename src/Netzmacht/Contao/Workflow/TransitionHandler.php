<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow;

use ContaoCommunityAlliance\DcGeneral\InputProviderInterface as InputProvider;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Entity\EntityRepository;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Exception\InvalidTransitionException;
use Netzmacht\Contao\Workflow\Model\State;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Form\Form;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\Transaction\TransactionHandler;

/**
 * Class TransitionHandler handles the transition to another step in the workflow.
 *
 * @package Netzmacht\Contao\Workflow
 */
class TransitionHandler
{
    /**
     * The given entity.
     *
     * @var Entity
     */
    private $entity;

    /**
     * The current workflow.
     *
     * @var Workflow
     */
    private $workflow;

    /**
     * The transition name which will be handled.
     *
     * @var string
     */
    private $transitionName;

    /**
     * The form object for user input.
     *
     * @var Form
     */
    private $form;

    /**
     * Validation state.
     *
     * @var bool
     */
    private $validated;

    /**
     * The entity repository.
     *
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * The state repository.
     *
     * @var StateRepository
     */
    private $stateRepository;

    /**
     * The transaction handler.
     *
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * The transition context.
     *
     * @var Context
     */
    private $context;


    /**
     * Construct.
     *
     * @param Entity             $entity             The entity.
     * @param Workflow           $workflow           The current workflow.
     * @param string             $transitionName     The transition to be handled.
     * @param EntityRepository   $entityRepository   EntityRepository which stores changes.
     * @param StateRepository    $stateRepository    StateRepository which stores new states.
     * @param TransactionHandler $transactionHandler TransactionHandler take care of transactions.
     * @param Context            $context            The context of the transition.
     */
    public function __construct(
        Entity $entity,
        Workflow $workflow,
        $transitionName,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Context $context
    ) {
        $this->entity             = $entity;
        $this->workflow           = $workflow;
        $this->transitionName     = $transitionName;
        $this->entityRepository   = $entityRepository;
        $this->stateRepository    = $stateRepository;
        $this->transactionHandler = $transactionHandler;
        $this->context            = $context;
    }


    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * Get the entity.
     *
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get the transition name.
     *
     * @return string
     *
     * @throws Flow\Exception\TransitionNotFoundException If transition was not found.
     */
    public function getTransitionName()
    {
        return $this->getTransition()->getName();
    }

    /**
     * Get the transition.
     *
     * @return Transition
     *
     * @throws Flow\Exception\TransitionNotFoundException If transition was not found.
     */
    public function getTransition()
    {
        if ($this->isStartTransition()) {
            return $this->workflow->getStartTransition();
        }

        return $this->workflow->getTransition($this->transitionName);
    }

    /**
     * Get the input form.
     *
     * @return Form
     */
    public function getForm()
    {
        if (!$this->form) {
            $this->buildForm();
        }

        return $this->form;
    }

    /**
     * Consider if it handles a start transition.
     *
     * @return bool
     */
    public function isStartTransition()
    {
        if ($this->entity->getState()) {
            return false;
        }

        return true;
    }

    /**
     * Consider if input is required.
     *
     * @return bool
     */
    public function requiresInputData()
    {
        return $this->getTransition()->requiresInputData();
    }

    /**
     * Get the context.
     *
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Validate the input.
     *
     * @return bool
     */
    public function validate()
    {
        if (!$this->validated) {
            $this->validated = $this->getForm()->validate($this->context);
        }

        return $this->validated;
    }

    /**
     * Transit to next step.
     *
     * @throws InvalidTransitionException If transition was not validated.
     * @throws \Exception                 If some actions throws an unknown exception.
     *
     * @return State
     */
    public function transit()
    {
        $this->guardValidated();

        $this->transactionHandler->begin();

        try {
            if ($this->isStartTransition()) {
                $state = $this->workflow->start($this->entity, $this->context);
            } else {
                $state = $this->workflow->transit($this->entity, $this->transitionName, $this->context);
            }

            $this->stateRepository->add($state);

            if ($this->entity->getMeta(Entity::IS_CHANGED)) {
                $this->entityRepository->add($this->entity);
            }
        } catch (\Exception $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }

        $this->transactionHandler->commit();

        return $state;
    }

    /**
     * Build a new form.
     *
     * @return void
     */
    private function buildForm()
    {
        $this->form = new Form();
        $this->getTransition()->buildForm($this->form);
    }

    /**
     * Guard that transition was validated before.
     *
     * @throws InvalidTransitionException If transition was not validated.
     *
     * @return void
     */
    private function guardValidated()
    {
        if (!$this->validated) {
            throw new InvalidTransitionException($this->getTransition()->getName());
        }
    }
}
