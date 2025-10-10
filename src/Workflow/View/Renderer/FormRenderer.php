<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Transition;
use Override;
use Symfony\Component\Form\FormInterface as Form;

use function assert;

final class FormRenderer extends AbstractRenderer
{
    /**
     * Section name.
     */
    protected static string $section = 'form';

    #[Override]
    public function supports(View $view): bool
    {
        return $view->getContext() instanceof Transition && $view->getOption('form') instanceof Form;
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    protected function renderParameters(View $view): array
    {
        $form = $view->getOption('form');
        assert($form instanceof Form);

        return ['form' => $form->createView()];
    }
}
