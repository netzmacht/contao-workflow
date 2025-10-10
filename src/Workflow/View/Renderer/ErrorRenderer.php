<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Override;

final class ErrorRenderer extends AbstractRenderer
{
    /**
     * Section name.
     */
    protected static string $section = 'errors';

    /**
     * {@inheritDoc}
     */
    #[Override]
    protected function renderParameters(View $view): array
    {
        return [
            'errors' => $view->getOption('errors'),
        ];
    }

    #[Override]
    public function supports(View $view): bool
    {
        $errors = $view->getOption('errors');

        return $errors instanceof ErrorCollection && $errors->countErrors() > 0;
    }
}
