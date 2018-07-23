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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Form;

use Netzmacht\ContaoFormBundle\Form\FormGeneratorType;
use Netzmacht\ContaoWorkflowBundle\Form\Builder\ActionFormBuilder;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class FormActionFormFactory
 */
final class FormActionFormBuilder implements ActionFormBuilder
{
    /**
     * {@inheritDoc}
     */
    public function supports(Action $action): bool
    {
        return $action instanceof FormAction;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException When invalid action is passed.
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void
    {
        if (!$action instanceof FormAction) {
            throw new \RuntimeException();
        }

        $formBuilder->add(
            $action->getName(),
            FormGeneratorType::class,
            [
                'formId' => $action->getConfigValue('formId'),
                'ignore' => ['submit']
            ]
        );
    }
}
