<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Form;

/**
 * Interface FormBuilder describes the formbuilder being used to build the form instance.
 *
 * @package Netzmacht\Contao\Workflow\Form
 */
interface FormBuilder
{
    /**
     * Build a passed form.
     *
     * @param Form $form The form being build.
     *
     * @return void
     */
    public function build(Form $form);
}
