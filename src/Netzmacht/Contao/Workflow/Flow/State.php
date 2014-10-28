<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow;

use DateTime;
use Netzmacht\Contao\Workflow\Data\Data;

/**
 * Class State stores information of a current state of an entity.
 *
 * @package Netzmacht\Contao\Workflow\Flow
 */
class State
{
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
     * Construct.
     *
     * @param string   $transitionName The transition executed to reach the step.
     * @param string   $stepToName     The step reached after transition.
     * @param bool     $successful     Consider if transition was successful.
     * @param array    $data           Stored data.
     * @param DateTime $reachedAt      Time when state was reached.
     * @param array    $errors         List of errors.
     */
    public function __construct(
        $transitionName,
        $stepToName,
        $successful,
        array $data,
        DateTime $reachedAt,
        array $errors = array()
    ) {
        $this->transitionName = $transitionName;
        $this->stepName       = $stepToName;
        $this->successful     = $successful;
        $this->data           = $data;
        $this->reachedAt      = $reachedAt;
        $this->errors         = $errors;
    }

    /**
     * Init an new state.
     *
     * @return static
     */
    public static function init()
    {
        return new static(null, null, true, array(), new DateTime());
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
            $transition->getName(),
            $stepName,
            $success,
            $context->getProperties(),
            $dateTime,
            $context->getErrorCollection()->getErrors()
        );
    }
}
