<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Transition;

abstract class AbstractTransitionRenderer extends AbstractRenderer
{
    public function supports(View $view): bool
    {
        return $view->getContext() instanceof Transition;
    }
}
