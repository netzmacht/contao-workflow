<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Model;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use DateTime;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Condition\Transition;

/**
 * Class State stores information of a current state of an entity.
 *
 * @package Netzmacht\Contao\Workflow\Flow
 */
class State
{
    /**
     * @var int
     */
    private $stateId;

    /**
     * @var int
     */
    private $entityId;

    /**
     * @var string
     */
    private $providerName;

    /**
     * Store if transition was successful.
     *
     * @var bool
     */
    private $successful;

    /**
     * The last transition.
     *
     * @var string
     */
    private $transitionName;

    /**
     * The current step.
     *
     * @var string
     */
    private $stepName;

    /**
     * Date being stored.
     *
     * @var array
     */
    private $data = array();

    /**
     * Date when state was reached.
     *
     * @var DateTime
     */
    private $reachedAt;

    /**
     * List of errors.
     *
     * @var array
     */
    private $errors;

    /**
     * Name of the workflow.
     *
     * @var string
     */
    private $workflowName;

    /**
     * Construct.
     *
     * @param          $entityId
     * @param          $providerName
     * @param string   $workflowName   Workflow name.
     * @param string   $transitionName The transition executed to reach the step.
     * @param string   $stepToName     The step reached after transition.
     * @param bool     $successful     Consider if transition was successful.
     * @param array    $data           Stored data.
     * @param DateTime $reachedAt      Time when state was reached.
     * @param array    $errors         List of errors.
     * @param null     $stateId
     */
    public function __construct(
        $entityId,
        $providerName,
        $workflowName,
        $transitionName,
        $stepToName,
        $successful,
        array $data,
        DateTime $reachedAt,
        array $errors = array(),
        $stateId = null
    ) {
        $this->entityId       = $entityId;
        $this->providerName   = $providerName;
        $this->workflowName   = $workflowName;
        $this->transitionName = $transitionName;
        $this->stepName       = $stepToName;
        $this->successful     = $successful;
        $this->data           = $data;
        $this->reachedAt      = $reachedAt;
        $this->errors         = $errors;
        $this->stateId        = $stateId;
    }

    /**
     * @param Entity     $entity
     * @param Transition $transition
     * @param Context    $context
     * @param            $success
     *
     * @return \Netzmacht\Contao\Workflow\Model\State
     */
    public static function start(Entity $entity, Transition $transition, Context $context, $success)
    {
        $state = new State(
            $entity->getId(),
            $entity->getProviderName(),
            $transition->getWorkflow()->getName(),
            $transition->getName(),
            $transition->getStepTo()->getName(),
            $success,
            $context->getProperties(),
            new \DateTime(),
            $context->getErrorCollection()->getErrors()
        );

        return $state;
    }

    /**
     * Get step name.
     *
     * @return string
     */
    public function getStepName()
    {
        return $this->stepName;
    }

    /**
     * Get transition name.
     *
     * @return string
     */
    public function getTransitionName()
    {
        return $this->transitionName;
    }

    /**
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * Get state data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get reached at time.
     *
     * @return DateTime
     */
    public function getReachedAt()
    {
        return $this->reachedAt;
    }

    /**
     * Consider if state is successful.
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->successful;
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @return int
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Transit to a new state.
     *
     * @param Transition $transition The transition being performed.
     * @param Context    $context    The transition context.
     * @param bool       $success    The success state.
     *
     * @return static
     */
    public function transit(Transition $transition, Context $context, $success = true)
    {
        $dateTime = new DateTime();
        $stepName = $success ? $transition->getStepTo()->getName() : $this->stepName;

        return new static(
            $this->entityId,
            $this->providerName,
            $this->workflowName,
            $transition->getName(),
            $stepName,
            $success,
            $context->getProperties(),
            $dateTime,
            $context->getErrorCollection()->getErrors()
        );
    }
}
