<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\View;

use Netzmacht\Workflow\Flow\Exception\StepNotFoundException;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFoundException;

/**
 * Class AvailableTransitionsView is used to display an available transitions menu.
 *
 * @package Netzmacht\Contao\Workflow\View
 */
class AvailableTransitionsView extends AbstractItemView
{
    /**
     * Render he view.
     *
     * @return string
     *
     * @throws StepNotFoundException        If no step found.
     * @throws TransitionNotFoundException  If transition is not found.
     */
    public function render()
    {
        if (!$this->template) {
            $this->template = 'workflow_available_transitions';
        }

        $transitions = array();

        if (!$this->item->isWorkflowStarted()) {
            $transitions[] = $this->workflow->getStartTransition();
        } else {
            $step = $this->workflow->getStep($this->item->getCurrentStepName());

            foreach ($step->getAllowedTransitions() as $transitionName) {
                $transitions[] = $this->workflow->getTransition($transitionName);
            }
        }

        return $this->renderTemplate(array('transitions' => $transitions));
    }
}
