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

class State
{
    /**
     * @var bool
     */
    private $successful;

    /**
     * @var string
     */
    private $transitionName;

    /**
     * @var string
     */
    private $stepName;

    /**
     * @var array
     */
    private $data;

    /**
     * @var DateTime
     */
    private $reachedAt;

    /**
     * @var array
     */
    private $errors;

    /**
     * @param          $transitionName
     * @param          $stepToName
     * @param bool     $successful
     * @param array    $data
     * @param DateTime $reachedAt
     * @param array    $errors
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
     * @return static
     */
    public static function init()
    {
        return new static(null, null, true, array(), new DateTime());
    }

    /**
     * @return string
     */
    public function getStepName()
    {
        return $this->stepName;
    }

    /**
     * @return string
     */
    public function getTransitionName()
    {
        return $this->transitionName;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return DateTime
     */
    public function getReachedAt()
    {
        return $this->reachedAt;
    }

    /**
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->successful;
    }

    /**
     * @param Transition $transition
     * @param Context    $context
     * @param bool       $success
     *
     * @return static
     */
    public function transit(Transition $transition, Context $context, $success = true)
    {
        $dateTime = new DateTime();

        return new static(
            $transition->getName(),
            $transition->getStepTo()->getName(),
            $success,
            $context->getProperties(),
            $dateTime,
            $context->getErrorCollection()->getErrors()
        );
    }
}
