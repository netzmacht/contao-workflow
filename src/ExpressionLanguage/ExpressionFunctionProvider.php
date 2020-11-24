<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * Class ExpressionFunctionProvider provides default functions for the workflow related expression language.
 */
final class ExpressionFunctionProvider implements ExpressionFunctionProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction(
                'constant',
                // @codingStandardsIgnoreStart
                static function () {
                    return "throw new \\InvalidArgumentException('Cannot use the constant() function in the expression' 
                        . ' for security reasons.');";
                },
                static function () {
                    throw new \InvalidArgumentException(
                        'Cannot use the constant() function in the expression for security reasons.'
                    );
                }
            )
        ];
    }
}
