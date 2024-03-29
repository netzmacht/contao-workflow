<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle;

use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\ExpressionLanguageFunctionsPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\TransitionFormBuilderPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\ViewFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetzmachtContaoWorkflowBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ViewFactoryPass());
        $container->addCompilerPass(new TransitionFormBuilderPass());
        $container->addCompilerPass(new ExpressionLanguageFunctionsPass());
    }
}
