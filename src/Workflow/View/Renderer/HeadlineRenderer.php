<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class HeadlineRenderer
 */
class HeadlineRenderer extends AbstractRenderer
{
    /**
     * The section name.
     *
     * @var string
     */
    protected static $section = 'headline';

    /**
     * {@inheritdoc}
     */
    public function supports(View $view): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderParameters(View $view): array
    {
        $context = $view->getContext();

        switch (get_class($context)) {
            case Step::class:
                $headline = $this->renderStepHeadline($view, $context);
                break;

            case Transition::class:
                $headline = $this->renderTransitionHeadline($view, $context);
                break;

            case State::class:
                // TODO: Implement state headline
                // No break

            default:
                return [];
        }

        return ['headline' => $headline];
    }

    /**
     * Render the step headline.
     *
     * @param View $view The workflow item view.
     * @param Step $step The current step.
     *
     * @return array
     */
    protected function renderStepHeadline(View $view, Step $step): array
    {
        $workflow = $view->getWorkflow();

        return [$workflow->getLabel(), $step->getLabel()];
    }

    /**
     * Render the step headline.
     *
     * @param View       $view       The workflow item view.
     * @param Transition $transition The transition of the view context.
     *
     * @return array
     */
    protected function renderTransitionHeadline(View $view, Transition $transition): array
    {
        $workflow = $view->getWorkflow();
        $headline = [$workflow->getLabel()];
        $stepName = $view->getItem()->getCurrentStepName();

        if ($workflow->hasStep($stepName)) {
            $currentStep = $workflow->getStep($stepName);
            $headline[]  = $currentStep->getLabel();
        } elseif ($stepName) {
            $headline[] = $stepName;
        }

        $headline[] = $transition->getLabel();

        return $headline;
    }
}
