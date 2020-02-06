<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle;

use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\TransitionFormBuilderPass;
use Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass\ViewFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NetzmachtContaoWorkflowBundle
 */
final class NetzmachtContaoWorkflowBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ViewFactoryPass());
        $container->addCompilerPass(new TransitionFormBuilderPass());
    }
}
