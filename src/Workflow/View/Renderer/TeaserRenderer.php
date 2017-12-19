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

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class TransitionTeaserRenderer
 */
class TeaserRenderer extends AbstractRenderer
{
    /**
     * Section name.
     *
     * @var string
     */
    protected static $section = 'teaser';

    /**
     * @inheritDoc
     */
    public function supports(View $view): bool
    {
        return $view->getContext() instanceof Transition || $view->getContext() instanceof Step;
    }

    /**
     * {@inheritDoc}
     */
    protected function renderParameters(View $view): array
    {
        /** @var Transition|Step $context */
        $context     = $view->getContext();
        $workflow    = $view->getWorkflow();
        $stepName    = $view->getItem()->getCurrentStepName();
        $currentStep = null;

        if ($workflow->hasStep($stepName)) {
            $currentStep = $workflow->getStep($stepName);
        }

        return [
            'headline'    => $context->getLabel(),
            'description' => $context->getConfigValue('description'),
            'workflow'    => $view->getWorkflow(),
            'currentStep' => $currentStep,
            'item'        => $view->getItem()
        ];
    }
}
