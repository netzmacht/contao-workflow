<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Action;

use Netzmacht\Contao\Workflow\Action;
use Netzmacht\Contao\Workflow\Form\Form;
use Netzmacht\Contao\Workflow\Form\FormBuilder;

/**
 * Class AbstractAction implements base action.
 *
 * @package Netzmacht\Contao\Workflow\Action
 */
abstract class AbstractAction implements Action
{
    /**
     * Given form builder.
     *
     * @var FormBuilder
     */
    private $formBuilder;

    /**
     * Construct.
     *
     * @param FormBuilder $formBuilder The form builder.
     */
    public function __construct(FormBuilder $formBuilder = null)
    {
        $this->formBuilder = $formBuilder;
    }

    /**
     * Consider if input is required.
     *
     * @return bool
     */
    public function requiresInputData()
    {
        if ($this->formBuilder) {
            return true;
        }

        return false;
    }

    /**
     * Build the form.
     *
     * @param Form $form Form being build.
     *
     * @return void
     */
    public function buildForm(Form $form)
    {
        if ($this->formBuilder) {
            $this->formBuilder->build($form);
        }

    }
}
