<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Form;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

final class FormActionFactory implements ActionTypeFactory
{
    public function getCategory(): string
    {
        return 'default';
    }

    public function getName(): string
    {
        return 'form';
    }

    public function isPostAction(): bool
    {
        return false;
    }

    public function supports(Workflow $workflow): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config, Transition $transition): Action
    {
        return new FormAction('action_' . $config['id'], $config['label'], $config);
    }
}
