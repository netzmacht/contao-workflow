<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;

use function get_class;

final class HeadlineRenderer extends AbstractRenderer
{
    /**
     * The section name.
     *
     * @var string
     */
    protected static $section = 'headline';

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
                // @codingStandardsIgnoreStart
                // TODO: Implement state headline
                // @codingStandardsIgnoreEnd
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
     * @return list<string>
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
     * @return list<string>
     */
    protected function renderTransitionHeadline(View $view, Transition $transition): array
    {
        $workflow = $view->getWorkflow();
        $headline = [$workflow->getLabel()];
        $stepName = $view->getItem()->getCurrentStepName();

        if ($stepName && $workflow->hasStep($stepName)) {
            $currentStep = $workflow->getStep($stepName);
            $headline[]  = $currentStep->getLabel();
        } elseif ($stepName) {
            $headline[] = $stepName;
        }

        $headline[] = $transition->getLabel();

        return $headline;
    }
}
