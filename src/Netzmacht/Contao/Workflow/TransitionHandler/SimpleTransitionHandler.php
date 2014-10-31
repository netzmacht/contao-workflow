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

use Netzmacht\Contao\Workflow\Entity\EntityRepository;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Exception\Flow\InvalidTransitionException;
use Netzmacht\Contao\Workflow\Exception\Flow\TransitionNotFoundException;
use Netzmacht\Contao\Workflow\Exception\Flow\WorkflowException;
use Netzmacht\Contao\Workflow\Flow\Step;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Item;
use Netzmacht\Contao\Workflow\Model\State;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Form\Form;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\Transaction\TransactionHandler;
use Netzmacht\Contao\Workflow\TransitionHandler;

/**
 * Class TransitionHandler handles the transition to another step in the workflow.
 *
 * @package Netzmacht\Contao\Workflow
 */
class SimpleTransitionHandler implements TransitionHandler
{
    /**
     * The given entity.
     *
     * @var Item
     */
    private $item;

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
     * @param Item               $item               The item.
     * @param Workflow           $workflow           The current workflow.
     * @param string             $transitionName     The transition to be handled.
     * @param EntityRepository   $entityRepository   EntityRepository which stores changes.
     * @param StateRepository    $stateRepository    StateRepository which stores new states.
     * @param TransactionHandler $transactionHandler TransactionHandler take care of transactions.
     * @param Context            $context            The context of the transition.
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        $transitionName,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Context $context
    ) {
        $this->item               = $item;
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
     * Get the item.
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
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
     * Get the transition.
     *
     * @return Transition
     *
     * @throws TransitionNotFoundException If transition was not found.
     */
    public function getTransition()
    {
        if ($this->isWorkflowStarted()) {
            return $this->workflow->getTransition($this->transitionName);
        }

        return $this->workflow->getStartTransition();
    }

    /**
     * Get current step. Will return null if workflow is not started yet.
     *
     * @return Step|null
     */
    public function getCurrentStep()
    {
        if($this->isWorkflowStarted()) {
            $stepName = $this->item->getCurrentStepName();

            return $this->workflow->getStep($stepName);
        }

        return null;
    }

    /**
     * Consider if it handles a start transition.
     *
     * @return bool
     */
    public function isWorkflowStarted()
    {
        return $this->item->isWorkflowStarted();
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
     * @return State
     * @throws InvalidTransitionException
     * @throws WorkflowException
     * @throws \Exception
     */
    public function start()
    {
        $this->guardNotStarted();
        $this->guardValidated();

        return $this->doStateTransition(
            function(Workflow $workflow, Item $item, Context $context) {
                return $workflow->start($item, $context);
            }
        );
    }

    /**
     * Transit to next step.
     *
     * @throws InvalidTransitionException
     * @throws \Exception If some actions throws an unknown exception.
     *
     * @return State
     */
    public function transit()
    {
        $this->guardAllowedTransition($this->transitionName);
        $this->guardValidated();

        $transitionName = $this->transitionName;

        return $this->doStateTransition(
            function (Workflow $workflow, Item $item, Context $context) use ($transitionName){
                return $workflow->transit($item, $transitionName, $context);
            }
        );
    }

    /**
     * Build the form for a transition.
     *
     * @throws TransitionNotFoundException
     * @return void
     */
    private function buildForm()
    {
        $this->form = new Form();
        $this->getTransition()->buildForm($this->form);
    }

    /**
     * Execute a state transition. Transition will be handled as an transaction.
     *
     * @param callable $processor
     *
     * @return State
     * @throws \Exception
     */
    private function doStateTransition($processor)
    {
        $this->transactionHandler->begin();

        try {
            $state = call_user_func($processor, $this->workflow, $this->item, $this->context);

            $this->stateRepository->add($state);
            $this->entityRepository->add($this->item->getEntity());
        } catch (\Exception $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }

        $this->transactionHandler->commit();

        return $state;
    }

    /**
     * Guard that transition was validated before.
     *
     * @throws WorkflowException
     *
     * @return void
     */
    private function guardValidated()
    {
        if (!$this->validated) {
            throw new WorkflowException('Transition has to be validated.');
        }
    }

    /**
     * Guard that requested transition is allowed.
     *
     * @param string $transitionName Transition to be processed.
     *
     * @throws WorkflowException If Transition is not allowed.
     *
     * @return void
     */
    private function guardAllowedTransition($transitionName)
    {
        if (!$this->isWorkflowStarted()) {
            throw new WorkflowException(sprintf(
                    'Not allowed to process transition "%s". Workflow "%s" not started for item "%s::%s"',
                    $transitionName,
                    $this->workflow->getName(),
                    $this->item->getEntity()->getProviderName(),
                    $this->item->getEntity()->getId()
                )
            );
        }

        $step = $this->getCurrentStep();

        if ($step->isTransitionAllowed($transitionName)) {
            throw new WorkflowException(sprintf(
                    'Not allowed to process transition "%s". Transition is not allowed in step "%s"',
                    $transitionName,
                    $step->getWorkflow()
                )
            );
        }
    }

    /**
     * Guard that workflow was not started.
     *
     * @throws WorkflowException If workflow was started
     *
     * @return void
     */
    private function guardNotStarted()
    {
        if ($this->isWorkflowStarted()) {
            throw new WorkflowException(sprintf(
                    'Workflow "%s" for item "%s::%s" is already started',
                    $this->workflow->getName(),
                    $this->item->getEntity()->getProviderName(),
                    $this->item->getEntity()->getId()
                )
            );
        }
    }

}
