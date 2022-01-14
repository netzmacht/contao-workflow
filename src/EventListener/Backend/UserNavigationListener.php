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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Backend;

use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class UserNavigationListener
 */
final class UserNavigationListener
{
    /**
     * Request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * UserNavigationListener constructor.
     *
     * @param RequestStack $requestStack  Request stack.
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Handle the getUserNavigation hook to determine if workflow module is used.
     *
     * @param array $modules User navigation modules.
     *
     * @return array
     */
    public function onGetUserNavigation(array $modules)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return $modules;
        }

        $module = $request->attributes->get('module');

        if ($request->attributes->get('_backend_module') === 'workflow') {
            foreach ($modules as $group => $groupModules) {
                if (isset($groupModules['modules'][$module])) {
                    $modules[$group]['modules'][$module]['isActive'] = true;
                }
            }
        }

        return $modules;
    }
}
