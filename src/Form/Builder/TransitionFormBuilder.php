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

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form\Builder;

use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Interface TransitionFormBuilder
 */
interface TransitionFormBuilder
{
    /**
     * Check if form builder supports the transition.
     *
     * @param Transition $transition Workflow transition.
     *
     * @return bool
     */
    public function supports(Transition $transition): bool;

    /**
     * Build the transition form.
     *
     * @param Transition  $transition  The workflow transition.
     * @param FormBuilder $formBuilder The form builder.
     *
     * @return void
     */
    public function buildForm(Transition $transition, FormBuilder $formBuilder): void;
}
