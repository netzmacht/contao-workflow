<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Note;

use AdamQuaile\Bundle\FieldsetBundle\Form\FieldsetType;
use Netzmacht\ContaoWorkflowBundle\Form\Builder\ActionFormBuilder;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedAction;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class NoteActionFormBuilder implements ActionFormBuilder
{
    public function supports(Action $action): bool
    {
        return $action instanceof NoteAction;
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedAction When invalid action is given.
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void
    {
        if (! $action instanceof NoteAction) {
            throw UnsupportedAction::withUnexpectedClass($action, NoteAction::class);
        }

        $constraints = [];
        $attributes  = [];

        if ($action->required()) {
            $constraints[] = new NotBlank();

            if ($action->minLength() > 0) {
                $constraints[]           = new Length(['min' => $action->minLength()]);
                $attributes['minlength'] = $action->minLength();
            }
        }

        $formBuilder->add(
            'action_' . $action->getConfigValue('id'),
            FieldsetType::class,
            [
                'legend' => $action->getLabel(),
                'fields' => [
                    [
                        'name' => $action->payloadName(),
                        'type' => TextareaType::class,
                        'attr' => [
                            'constraints' => $constraints,
                            'label'       => $action->getConfigValue('description'),
                            'required'    => $action->required(),
                            'attr'        => $attributes,
                        ],
                    ],
                ],
            ]
        );
    }
}
