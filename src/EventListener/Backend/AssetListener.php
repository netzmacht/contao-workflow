<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Backend;

use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class AssetListener
{
    /** @var AssetsManager */
    private $assetsManager;

    /** @var RequestScopeMatcher */
    private $scopeMatcher;

    public function __construct(AssetsManager $assetsManager, RequestScopeMatcher $scopeMatcher)
    {
        $this->assetsManager = $assetsManager;
        $this->scopeMatcher  = $scopeMatcher;
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
