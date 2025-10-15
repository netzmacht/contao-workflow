<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Netzmacht\Contao\Toolkit\NetzmachtContaoToolkitBundle;
use Netzmacht\ContaoWorkflowBundle\NetzmachtContaoWorkflowBundle;
use Override;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

final class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
     /** {@inheritDoc} */
    #[Override]
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(NetzmachtContaoWorkflowBundle::class)
                ->setLoadAfter(
                    [
                        ContaoCoreBundle::class,
                        NetzmachtContaoToolkitBundle::class,
                    ],
                )
                ->setReplace(['workflow']),
        ];
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    #[Override]
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel): RouteCollection|null
    {
        $loader = $resolver->resolve(__DIR__ . '/../Resources/config/routing.yml');
        if ($loader === false) {
            return null;
        }

        return $loader->load(__DIR__ . '/../Resources/config/routing.yml');
    }
}
