<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Definition\Database;


use Netzmacht\Workflow\Contao\Backend\Event\GetWorkflowPermissionsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PermissionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            GetWorkflowPermissionsEvent::NAME => 'getPermissions',
        );
    }

    public function getPermissions(GetWorkflowPermissionsEvent $event)
    {
        $workflow    = $event->getWorkflowModel();
        $permissions = deserialize($workflow->permissions, true);

        foreach ($permissions as $config) {
            $event->addPermission($config['name'], 'workflow');
        }

        $event
            ->addPermission('be_admin', 'contao')
            ->addPermission('fe_guest', 'contao');
    }
}
