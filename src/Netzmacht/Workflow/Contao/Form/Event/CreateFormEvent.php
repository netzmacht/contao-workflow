<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Form\Event;

use Netzmacht\Workflow\Contao\Form\Form;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateFormEvent is emitted when a form is created.
 *
 * @package Netzmacht\Workflow\Contao\Form\Event
 */
class CreateFormEvent extends Event
{
    const NAME = 'workflow.factory.create-form';

    /**
     * Form being created.
     *
     * @var Form
     */
    private $form;

    /**
     * Form type.
     *
     * @var string
     */
    private $type;

    /**
     * Construct.
     *
     * @param string $type Form type.
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Get the form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set the crated form.
     *
     * @param Form $form The created form.
     *
     * @return $this
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get the form type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
