<?php

declare(strict_types=1);

namespace spec\Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass;

use Contao\ManagerPlugin\Config\ContainerBuilder;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\ExpressionLanguageFunctionsPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\Reference;

final class ExpressionLanguageFunctionsPassSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ExpressionLanguageFunctionsPass::class);
    }

    public function it_registers_expression_language_functions(
        ContainerBuilder $container,
        ParameterBagInterface $parameterBag,
        Definition $definition,
        Definition $barDefinition,
        Definition $bazDefinition
    ): void {
        $container->hasDefinition('netzmacht.contao_workflow.expression_language')
            ->shouldBeCalledOnce()
            ->willReturn(true);

        $container->getDefinition('netzmacht.contao_workflow.expression_language')
            ->shouldBeCalledOnce()
            ->willReturn($definition);

        $container->getDefinition('foo.bar')
            ->shouldBeCalledOnce()
            ->willReturn($barDefinition);

        $container->getDefinition('foo.baz')
            ->shouldBeCalledOnce()
            ->willReturn($bazDefinition);

        $this->describeTaggedService($barDefinition, 'Foo\Bar');
        $this->describeTaggedService($bazDefinition, 'Foo\Baz');

        $references = [
            new Reference('foo.bar'),
            new Reference('foo.baz'),
        ];

        $container->getParameterBag()->willReturn($parameterBag);

        $container->findTaggedServiceIds('netzmacht.contao_workflow.expression_language.function_provider', true)
            ->shouldBeCalledOnce()
            ->willReturn(
                [
                    'foo.bar' => [
                        ['name' => 'netzmacht.contao_workflow.expression_language.function_provider'],
                    ],
                    'foo.baz' => [
                        ['name' => 'netzmacht.contao_workflow.expression_language.function_provider'],
                    ],
                ]
            );

        $definition->setArgument(1, $references)
            ->shouldBeCalledOnce();

        $this->process($container);
    }

    private function describeTaggedService(Definition $definition, string $className): void
    {
        $definition->getClass()
            ->shouldBeCalledOnce()
            ->willReturn($className);

        $definition->isAutoconfigured()->willReturn(false);

        $definition
            ->hasTag('netzmacht.contao_workflow.expression_language.function_provider')
            ->willReturn(true);

        $definition
            ->hasTag('netzmacht.contao_workflow.expression_language.function_provider')
            ->willReturn(true);
    }
}
