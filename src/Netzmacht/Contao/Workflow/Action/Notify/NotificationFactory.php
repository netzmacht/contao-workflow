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

interface NotificationFactory
{
    /**
     * @param $identifier
     * @return Notification
     */
    public function create($identifier);
}
