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

namespace Netzmacht\ContaoWorkflowBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Netzmacht\Contao\Toolkit\Bundle\NetzmachtContaoToolkitBundle;
use Netzmacht\ContaoWorkflowBundle\NetzmachtContaoWorkflowBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class Plugin
 */
final class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    /**
     * {@inheritDoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(NetzmachtContaoWorkflowBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, NetzmachtContaoToolkitBundle::class])
                ->setReplace(['workflow'])
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel): ?RouteCollection
    {
        $loader = $resolver->resolve(__DIR__ . '/../Resources/config/routing.yml');
        if ($loader === false) {
            return null;
        }

        return $loader->load(__DIR__ . '/../Resources/config/routing.yml');
    }
}
