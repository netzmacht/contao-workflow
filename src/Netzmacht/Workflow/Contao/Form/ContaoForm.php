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

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Workflow\Form\Form;

/**
 * Interface Form describes a form instance which is used for workflow transition.
 *
 * @package Netzmacht\Workflow\Form
 */
interface ContaoForm extends Form
{
    /**
     * Set form name.
     *
     * @param string $name Form name.
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get form name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get entity.
     *
     * @return Entity
     */
    public function getEntity();

    /**
     * Add form.
     *
     * @param FormType $form Child form.
     *
     * @return $this
     */
    public function addForm(FormType $form);

    /**
     *
     * @param string $name Form name.
     *
     * @return FormType
     *
     * @throws \InvalidArgumentException If form does not exists.
     */
    public function getForm($name);

    /**
     * Get all sub forms.
     *
     * @return FormType[]
     */
    public function getForms();

    /**
     * Render the form.
     *
     * @return string
     */
    public function render();
}
