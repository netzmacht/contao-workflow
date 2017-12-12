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

namespace Netzmacht\ContaoWorkflowBundle;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\AddTaggedServicesAsArgumentPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\ActionFactoriesPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\ActionFormBuilderPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\EntityRepositoryFactoryPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\HistoryRendererPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\TransitionActionFormBuilderPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\WorkflowTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NetzmachtContaoWorkflowBundle
 */
class NetzmachtContaoWorkflowBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new WorkflowTypePass())
            ->addCompilerPass(new ActionFactoriesPass())
            ->addCompilerPass(new ActionFormBuilderPass())
            ->addCompilerPass(new TransitionActionFormBuilderPass())
            ->addCompilerPass(new EntityRepositoryFactoryPass())
            ->addCompilerPass(new HistoryRendererPass())
            ->addCompilerPass(
                new AddTaggedServicesAsArgumentPass(
                    'netzmacht.contao_workflow.entity_factory',
                    'netzmacht.contao_workflow.entity_factory'
                )
            );
    }
}
