<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\ExpressionLanguage;

use InvalidArgumentException;
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
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.MultiLineNotAllowed
        return [
            new ExpressionFunction(
                'constant',
                // @codingStandardsIgnoreStart
                static function (): string {
                    return "throw new \\InvalidArgumentException('Cannot use the constant() function in the expression' 
                        . ' for security reasons.');";
                },
                static function (): void {
                    throw new InvalidArgumentException(
                        'Cannot use the constant() function in the expression for security reasons.'
                    );
                }
            ),
        ];
        // phpcs:enable Squiz.Arrays.ArrayDeclaration.MultiLineNotAllowed
    }
}
