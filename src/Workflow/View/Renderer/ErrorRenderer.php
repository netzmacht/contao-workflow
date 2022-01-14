<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;

final class ErrorRenderer extends AbstractRenderer
{
    /**
     * Section name.
     *
     * @var string
     */
    protected static $section = 'errors';

    /**
     * {@inheritDoc}
     */
    protected function renderParameters(View $view): array
    {
        return [
            'errors' => $view->getOption('errors'),
        ];
    }

    public function supports(View $view): bool
    {
        $errors = $view->getOption('errors');

        return $errors instanceof ErrorCollection && $errors->countErrors() > 0;
    }
}
