<?php

declare(strict_types=1);

namespace spec\Netzmacht\ContaoWorkflowBundle;

use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\ExpressionLanguageFunctionsPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\TransitionFormBuilderPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\ViewFactoryPass;
use Netzmacht\ContaoWorkflowBundle\NetzmachtContaoWorkflowBundle;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class NetzmachtContaoWorkflowBundleSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(NetzmachtContaoWorkflowBundle::class);
    }

    public function it_registers_compiler_passes(ContainerBuilder $container): void
    {
        $container->addCompilerPass(Argument::type(ViewFactoryPass::class))->shouldBeCalledOnce();
        $container->addCompilerPass(Argument::type(TransitionFormBuilderPass::class))->shouldBeCalledOnce();
        $container->addCompilerPass(Argument::type(ExpressionLanguageFunctionsPass::class))->shouldBeCalledOnce();

        $this->build($container);
    }
}
