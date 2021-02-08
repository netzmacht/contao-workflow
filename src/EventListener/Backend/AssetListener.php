<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Benedict Massolle <bm@presentprogressive.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Backend;

use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;

class AssetListener
{
    /**
     * Asset Manager
     *
     * @var AssetsManager
     */
    private AssetsManager $assetsManager;

    public function __construct(AssetsManager $assetsManager)
    {
        $this->assetsManager = $assetsManager;
    }

    public function addBackendAssets()
    {
        $this->assetsManager->addStylesheet('bundles/netzmachtcontaoworkflow/css/backend.css');
    }
}