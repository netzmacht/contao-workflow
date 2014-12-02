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

use Netzmacht\Workflow\Contao\Form\Contao\BackendForm;
use Netzmacht\Workflow\Factory\Event\CreateFormEvent;
use Netzmacht\Workflow\Form\Form;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FormFactory create Contao forms for the frontend or the backend.
 *
 * @package Netzmacht\Workflow\Contao\Form
 */
class FormFactory implements EventSubscriberInterface
{
    const BE = 'BE';
    const FE = 'FE';

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
     * Handle the create event.
     *
     * @param CreateFormEvent $event The event.
     *
     * @return void
     */
    public function handleCreateForm(CreateFormEvent $event)
    {
        if ($event->getForm()) {
            return;
        }

        $form = $this->createForm($event->getType(), $event->getName());
        $event->setForm($form);
    }

    /**
     * Create the form.
     *
     * @param string $type The form type.
     * @param string $name The form name.
     *
     * @return Form
     *
     */
    public function createForm($type, $name)
    {
        switch($type) {
            case static::BACKEND:
            case static::BE:
                return new BackendForm($name);

//@codingStandardsIgnoreStart
// At the moment only backend forms are supported.
// Frontend forms still have to be implemented.
//            case static::FRONTEND:
//                return new FrontendForm();
//@codingStandardsIgnoreEnd

            default:
                throw new \InvalidArgumentException(sprintf('Form type "%s" could not be created', $type));
        }
    }
}
