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

abstract class AbstractAction implements Action
{
    /**
     * @var FormBuilder
     */
    private $formBuilder;

    /**
     * @param $formBuilder
     */
    function __construct(FormBuilder $formBuilder=null)
    {
        $this->formBuilder = $formBuilder;
    }

    /**
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
     * @param Form $form
     * @return void
     */
    public function buildForm(Form $form)
    {
        $this->formBuilder->build($form);
    }

} 