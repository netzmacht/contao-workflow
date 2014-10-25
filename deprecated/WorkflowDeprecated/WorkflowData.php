<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated;


use Netzmacht\Contao\WorkflowDeprecated\Flow\Transition;
use Netzmacht\Contao\WorkflowDeprecated\Flow\Workflow;

class WorkflowData
{
    const STATE_START   = 'start';
    const STATE_SUCCESS = 'success';
    const STATE_FAILED  = 'failed';

    /**
     * @var string
     */
    private $step;

    /**
     * @var string
     */
    private $state;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $reference;

    /**
     * @param $reference
     * @param $step
     * @param $state
     * @param array $data
     */
    private function __construct($reference, $step, $state, array $data=null)
    {
        $this->reference = $reference;
        $this->step      = $step;
        $this->state     = $state;
        $this->data      = $data;
    }

    /**
     * @param Entity $entity
     * @param Workflow $workflow
     * @return static
     */
    public static function initiate(Entity $entity, Workflow $workflow)
    {
        $reference    = sprintf('%s::%s', $entity->getProviderName(), $entity->getId());
        $workflowData = new static($reference, $workflow->getStartStep()->getName(), static::STATE_START);

        $entity->setWorkflowState($workflowData);

        return $workflowData;
    }

    /**
     * @param Entity $entity
     * @param Transition $transition
     * @param array $data
     * @param $state
     * @return static
     */
    public function transit(Entity $entity, Transition $transition, $data=array(), $state)
    {
        $state        = $state ? static::STATE_SUCCESS : static::STATE_FAILED;
        $workflowData = new static($this->reference, $transition->getTarget(), $state, $data);

        $entity->setWorkflowState($workflowData);

        return $workflowData;
    }

    /**
     * @return string
     */
    public function getStepName()
    {
        return $this->step;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
} 