<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Action;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Interface ActionFactory
 *
 * @package Netzmacht\Contao\Workflow\Action
 */
interface ActionTypeFactory
{
    /**
     * Get name of the action.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Create an action.
     *
     * @param array      $config     Action config.
     * @param Workflow   $workflow   Workflow in which the action occurs.
     * @param Transition $transition Transition to which the action belongs.
     *
     * @return Action
     */
    public function create(array $config, Workflow $workflow, Transition $transition): Action;

    /**
     * Build the form.
     *
     * @param FormBuilder $formBuilder The form builder.
     * @param array       $config      The action config.
     *
     * @return void
     */
    public function buildForm(FormBuilder $formBuilder, array $config): void;
}
