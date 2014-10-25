<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Action\Notify;

use NotificationCenter\Model\Notification;

/**
 * Class ContaoModelNotificationFactory will create a notification calling the.
 *
 * Interfaced and because of testing extracted to a mapper because of testing purpose
 *
 * @package Netzmacht\Contao\Workflow\Action\Notify
 */
class ContaoModelNotificationFactory implements NotificationFactory
{
    /**
     * @param $identifier
     * @return Notification
     */
    public function create($identifier)
    {
        $notification = Notification::findByPK($identifier);

        if (!$notification) {
            throw new \InvalidArgumentException(sprintf('Notification "%s" not found', $identifier));
        }

        return $notification;
    }

} 