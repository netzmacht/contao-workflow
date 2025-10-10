<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Backend;

use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class AssetListener
{
    public function __construct(private AssetsManager $assetsManager, private RequestScopeMatcher $scopeMatcher)
    {
    }

    /**
     * Adds extensions' stylesheet to back-end.
     */
    public function addBackendAssets(RequestEvent $event): void
    {
        if (! $this->scopeMatcher->isBackendRequest($event->getRequest())) {
            return;
        }

        $this->assetsManager->addStylesheet('bundles/netzmachtcontaoworkflow/css/backend.css');
    }
}
