<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class HistoryRendererPass
 */
class HistoryRendererPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netzmacht.contao_workflow.view.history_renderer')) {
            return;
        }

        $definition = $container->getDefinition('netzmacht.contao_workflow.view.history_renderer');
        $references = $this->findAndSortTaggedServices(
            'netzmacht.contao_workflow.history_renderer',
            $container
        );

        $definition->setArgument(0, $references);
    }
}
