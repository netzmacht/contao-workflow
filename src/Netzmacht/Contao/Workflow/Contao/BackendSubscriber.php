<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Contao;

use Netzmacht\Contao\Workflow\Contao\Dca\Event\GetWorkflowActionsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BackendSubscriber subscribes to events being raised in the backend interface.
 *
 * @package Netzmacht\Contao\Workflow\Contao
 */
class BackendSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array();
    }
}
