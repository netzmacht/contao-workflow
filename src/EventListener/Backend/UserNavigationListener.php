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

namespace Netzmacht\Contao\Workflow\EventListener\Backend;


use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface as Router;

class UserNavigationListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Router
     */
    private $router;

    /**
     * UserNavigationListener constructor.
     *
     * @param RequestStack    $requestStack
     * @param Router $router
     */
    public function __construct(RequestStack $requestStack, Router $router)
    {
        $this->requestStack = $requestStack;
        $this->router       = $router;
    }

    /**
     * @param array $modules
     *
     * @return array
     */
    public function onGetUserNavigation(array $modules)
    {
        $request = $this->requestStack->getCurrentRequest();
        $module  = $request->query->get('module');

        if ($request->attributes->get('_backend_module') === 'workflow') {
            foreach ($modules as $group => $groupModules) {
                if (isset($groupModules['modules'][$module])) {
                    $modules[$group]['modules'][$module]['isActive'] = true;
                }
            }

            $GLOBALS['TL_CSS'][] = 'bundles/netzmachtcontaoworkflow/css/backend.css';
        }

        return $modules;
    }
}
