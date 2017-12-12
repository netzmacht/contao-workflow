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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Note;

use Netzmacht\ContaoWorkflowBundle\Form\Builder\ActionFormBuilder;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class NoteActionFormBuilder
 */
class NoteActionFormBuilder implements ActionFormBuilder
{
    /**
     * {@inheritDoc}
     */
    public function supports(Action $action): bool
    {
        return $action instanceof NoteAction;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void
    {
        if (!$action instanceof NoteAction) {
            throw new \RuntimeException();
        }

        /** @var NoteAction $action */
        $formBuilder->add(
            'action_' . $action->getConfigValue('id') . '_note',
            TextareaType::class,
            [
                'label' => $action->getConfigValue('label'),
                'attr'  => [
                    'help' => $action->getConfigValue('description'),
                ],
            ]
        );
    }
}
