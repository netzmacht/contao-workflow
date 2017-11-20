<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Form;

use Netzmacht\Contao\Workflow\Form\Contao\BackendForm;
use Netzmacht\Workflow\Factory\Event\CreateFormEvent;
use Netzmacht\Workflow\Form\Form;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FormFactory create Contao forms for the frontend or the backend.
 *
 * @package Netzmacht\Contao\Workflow\Form
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
     * @throws \InvalidArgumentException If form type is not supported.
     */
    public function createForm($type, $name)
    {
        switch ($type) {
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
