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
use Netzmacht\Contao\Workflow\Flow\Exception\InValidTransitionException;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Form\Form;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\Transaction\TransactionHandler;

class TransitionHandler
{
    /**
     * @var Entity
     */
    private $entity;

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var string
     */
    private $transitionName;

    /**
     * @var Form
     */
    private $form;

    /**
     * @var bool
     */
    private $validated;

    /**
     * @var ErrorCollection
     */
    private $errors;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var StateRepository
     */
    private $stateRepository;

    /**
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * @var Context
     */
    private $context;


    /**
     * @param Entity             $entity
     * @param Workflow           $workflow
     * @param                    $transitionName
     * @param EntityRepository   $entityRepository
     * @param StateRepository    $stateRepository
     * @param TransactionHandler $transactionHandler
     * @param InputProvider      $inputProvider
     */
    public function __construct(
        Entity $entity,
        Workflow $workflow,
        $transitionName,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        InputProvider $inputProvider
    ) {
        $this->entity             = $entity;
        $this->workflow           = $workflow;
        $this->transitionName     = $transitionName;
        $this->entityRepository   = $entityRepository;
        $this->stateRepository    = $stateRepository;
        $this->transactionHandler = $transactionHandler;
        $this->context            = new Context($inputProvider);
        $this->errors             = new ErrorCollection();
    }


    /**
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return Transition
     */
    public function getTransitionName()
    {
        return $this->getTransition()->getName();
    }

    /**
     * @return Transition
     * @throws Flow\Exception\TransitionNotFoundException
     */
    public function getTransition()
    {
        if ($this->isStartTransition()) {
            return $this->workflow->getStartTransition();
        }

        return $this->workflow->getTransition($this->transitionName);
    }

    /**
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
     * @return bool
     */
    public function requiresInputData()
    {
        return $this->getTransition()->requiresInputData();
    }

    /**
     * @return ErrorCollection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if (!$this->validated) {
            $this->validated = $this->getForm()->validate();
        }

        return $this->validated;
    }

    /**
     * @throws InValidTransitionException
     */
    public function transit()
    {
        $this->guardValidated();

        $this->transactionHandler->begin();

        try {
            if ($this->isStartTransition()) {
                $state = $this->workflow->start($this->entity, $this->context, $this->errors);
            } else {
                $state = $this->workflow->transit($this->entity, $this->transitionName, $this->context, $this->errors);
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
     * Build a new form
     */
    private function buildForm()
    {
        $this->form = new Form();
        $this->getTransition()->buildForm($this->form);
    }

    /**
     * @throws InValidTransitionException
     */
    private function guardValidated()
    {
        if (!$this->validated) {
            throw new InvalidTransitionException($this->getTransition()->getName());
        }
    }
}
