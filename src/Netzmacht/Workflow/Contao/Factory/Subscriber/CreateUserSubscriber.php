<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Factory\Subscriber;


use Netzmacht\Workflow\Factory\Event\CreateUserEvent;
use Netzmacht\Workflow\Security\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateUserSubscriber implements EventSubscriberInterface
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateUserEvent::NAME => 'loadPermissions'
        );
    }

    public function loadPermissions(CreateUserEvent $event)
    {
        $user = $event->getUser();

        if (TL_MODE == 'BE') {
            $this->loadBackendPermissions($user);
        } elseif (TL_MODE == 'FE') {
            $this->loadFrontendPermissions($user);
        }
    }

    private function loadBackendPermissions(User $user)
    {

    }

    private function loadFrontendPermissions(User $user)
    {
    }
}
