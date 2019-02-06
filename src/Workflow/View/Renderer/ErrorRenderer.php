<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;

/**
 * Class ErrorRenderer
 */
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
            'errors' => $view->getOption('errors')
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function supports(View $view): bool
    {
        $errors = $view->getOption('errors');

        return $errors instanceof ErrorCollection && $errors->countErrors() > 0;
    }
}
