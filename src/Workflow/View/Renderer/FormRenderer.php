<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormInterface as Form;

use function assert;

final class FormRenderer extends AbstractRenderer
{
    /**
     * Section name.
     *
     * @var string
     */
    protected static $section = 'form';

    public function supports(View $view): bool
    {
        return $view->getContext() instanceof Transition && $view->getOption('form') instanceof Form;
    }

    /**
     * {@inheritDoc}
     */
    protected function renderParameters(View $view): array
    {
        $form = $view->getOption('form');
        assert($form instanceof Form);

        return ['form' => $form->createView()];
    }
}
