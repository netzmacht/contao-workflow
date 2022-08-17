<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TransitionFormBuilderPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition('netzmacht.contao_workflow.form.transition_form_builder')) {
            return;
        }

        $definition   = $container->getDefinition('netzmacht.contao_workflow.form.transition_form_builder');
        $formBuilders = $this->findAndSortTaggedServices(
            'netzmacht.contao_workflow.transition_form_builder',
            $container
        );

        foreach ($formBuilders as $formBuilder) {
            $definition->addMethodCall('register', [$formBuilder]);
        }
    }
}
