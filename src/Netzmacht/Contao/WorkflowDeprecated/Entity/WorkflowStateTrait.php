<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Entity;


use Netzmacht\Contao\WorkflowDeprecated\WorkflowData;

trait WorkflowStateTrait
{

    /**
     * @return WorkflowData|null
     */
    public function getWorkflowState()
    {
        return $this->getMeta('workflow.state');
    }

    /**
     * @param WorkflowData $workflowData
     * @return $this
     */
    public function setWorkflowState(WorkflowData $workflowData)
    {
        $this->setMeta('workflow.data', $workflowData);

        return $this;
    }

} 