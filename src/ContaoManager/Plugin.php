<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Netzmacht\Contao\Toolkit\Bundle\NetzmachtContaoToolkitBundle;
use Netzmacht\ContaoWorkflowBundle\NetzmachtContaoWorkflowBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

final class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    /**
     * {@inheritDoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(SensioFrameworkExtraBundle::class),
            BundleConfig::create(NetzmachtContaoWorkflowBundle::class)
                ->setLoadAfter(
                    [
                        ContaoCoreBundle::class,
                        SensioFrameworkExtraBundle::class,
                        NetzmachtContaoToolkitBundle::class,
                    ]
                )
                ->setReplace(['workflow']),
        ];
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel): ?RouteCollection
    {
        $loader = $resolver->resolve(__DIR__ . '/../Resources/config/routing.yml');
        if ($loader === false) {
            return null;
        }

        return $loader->load(__DIR__ . '/../Resources/config/routing.yml');
    }
}
