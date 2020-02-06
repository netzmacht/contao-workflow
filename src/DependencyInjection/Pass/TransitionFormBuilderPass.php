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

namespace Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TransitionFormBuilderPass
 */
final class TransitionFormBuilderPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition('netzmacht.contao_workflow.form.transition_form_builder')) {
            return;
        }

        $definition = $container->getDefinition('netzmacht.contao_workflow.form.transition_form_builder');
        $formBuilders = $this->findAndSortTaggedServices(
            'netzmacht.contao_workflow.transition_form_builder',
            $container
        );

        foreach ($formBuilders as $formBuilder) {
            $definition->addMethodCall('register', [$formBuilder]);
        }
    }
}
