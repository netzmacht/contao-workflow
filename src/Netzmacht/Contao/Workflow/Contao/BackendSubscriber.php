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


use Netzmacht\Contao\Workflow\Contao\Dca\Event\GetWorkflowTypesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BackendSubscriber implements EventSubscriberInterface
{
    /**
     * @return array|void
     */
    public static function getSubscribedEvents()
    {
        return array(
            GetWorkflowTypesEvent::NAME => 'getWorkflowTypes',
        );
    }

    /**
     * @param GetWorkflowTypesEvent $event
     */
    public function getWorkflowTypes(GetWorkflowTypesEvent $event)
    {
        $event->addTypes('core', array('notify'));
    }
}
