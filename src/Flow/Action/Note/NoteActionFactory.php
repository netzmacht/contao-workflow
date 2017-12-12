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

namespace Netzmacht\ContaoWorkflowBundle\Flow\Action\Note;

use Netzmacht\ContaoWorkflowBundle\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class NoteActionFactory
 *
 * @package Netzmacht\ContaoWorkflowBundle\Flow\Action\Note
 */
class NoteActionFactory implements ActionTypeFactory
{
    /**
     * {@inheritDoc}
     */
    public function getCategory(): string
    {
        return 'default';
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'note';
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Workflow $workflow): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function match(Action $action): bool
    {
        return $action instanceof NoteAction;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config, Transition $transition): Action
    {
        return new NoteAction('action_' . $config['id'], $config['label'], $config);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void
    {
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
