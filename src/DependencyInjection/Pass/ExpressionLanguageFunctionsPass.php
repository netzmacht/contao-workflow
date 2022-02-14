<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ExpressionLanguageFunctionsPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition('netzmacht.contao_workflow.expression_language')) {
            return;
        }

        $definition     = $container->getDefinition('netzmacht.contao_workflow.expression_language');
        $taggedServices = $this->findAndSortTaggedServices(
            'netzmacht.contao_workflow.expression_language.function_provider',
            $container
        );

        $definition->setArgument(1, $taggedServices);
    }
}
