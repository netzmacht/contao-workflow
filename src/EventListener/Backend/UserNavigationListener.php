<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Backend;

use Symfony\Component\HttpFoundation\RequestStack;

final class UserNavigationListener
{
    /**
     * Request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack Request stack.
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Handle the getUserNavigation hook to determine if workflow module is used.
     *
     * @param array<string,array<string,array<string,mixed>>> $modules User navigation modules.
     *
     * @return array<string,array<string,array<string,mixed>>>
     */
    public function onGetUserNavigation(array $modules): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (! $request) {
            return $modules;
        }

        $module = $request->attributes->get('module');

        if ($request->attributes->get('_backend_module') === 'workflow') {
            foreach ($modules as $group => $groupModules) {
                if (! isset($groupModules['modules'][$module])) {
                    continue;
                }

                $modules[$group]['modules'][$module]['isActive'] = true;
            }
        }

        return $modules;
    }
}
