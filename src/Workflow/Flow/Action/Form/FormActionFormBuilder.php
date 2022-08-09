<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Form;

use Netzmacht\ContaoFormBundle\Form\FieldsetType;
use Netzmacht\ContaoFormBundle\Form\FormGeneratorType;
use Netzmacht\ContaoWorkflowBundle\Form\Builder\ActionFormBuilder;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedAction;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

final class FormActionFormBuilder implements ActionFormBuilder
{
    public function supports(Action $action): bool
    {
        return $action instanceof FormAction;
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedAction When invalid action is passed.
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void
    {
        if (! $action instanceof FormAction) {
            throw UnsupportedAction::withUnexpectedClass($action, FormAction::class);
        }

        if (! $action->getConfigValue('form_fieldset')) {
            $formBuilder->add(
                $action->getName(),
                FormGeneratorType::class,
                [
                    'formId' => $action->getConfigValue('form_formId'),
                    'ignore' => ['submit'],
                ]
            );

            return;
        }

        $formBuilder->add(
            'action_' . $action->getConfigValue('id') . '_fieldset',
            FieldsetType::class,
            [
                'legend' => $action->getLabel(),
                'fields' => [
                    [
                        'name' => 'action_' . $action->getConfigValue('id'),
                        'type' => FormGeneratorType::class,
                        'attr' => [
                            'formId' => $action->getConfigValue('form_formId'),
                            'ignore' => ['submit'],
                        ],
                    ],
                ],
            ]
        );
    }
}
