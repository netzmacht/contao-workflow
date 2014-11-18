<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Form;

use Netzmacht\Workflow\Contao\Form\Event\CreateFormEvent;
use Netzmacht\Workflow\Contao\Form\BackendForm;
use Netzmacht\Workflow\Form\Form;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormFactory implements EventSubscriberInterface
{
    const BE       = 'BE';
    const FE       = 'FE';

    const BACKEND  = 'backend';
    const FRONTEND = 'frontend';

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateFormEvent::NAME => 'handleCreateForm'
        );
    }

    /**
     * @param \Netzmacht\Workflow\Contao\Form\Event\CreateFormEvent $event
     *
     * @return void
     */
    public function handleCreateForm(CreateFormEvent $event)
    {
        if ($event->getForm()) {
            return;
        }

        $form = $this->create($event->getType());
        $event->setForm($form);
    }

    /**
     * @param $type
     *
     * @return Form
     */
    public function createForm($type)
    {
        switch($type) {
            case static::BACKEND:
            case static::BE:
                return new BackendForm();

// At the moment only backend forms are supported.
// Frontend forms still have to be implemented.
//            case static::FRONTEND:
//                return new FrontendForm();
        }
    }
}
