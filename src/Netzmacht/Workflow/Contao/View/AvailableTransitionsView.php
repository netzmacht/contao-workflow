<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\View;

use Netzmacht\Workflow\Flow\Exception\StepNotFoundException;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFoundException;

/**
 * Class AvailableTransitionsView is used to display an available transitions menu.
 *
 * @package Netzmacht\Workflow\Contao\View
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
