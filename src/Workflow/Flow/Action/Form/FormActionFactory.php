<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Form;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Override;

final class FormActionFactory implements ActionTypeFactory
{
    #[Override]
    public function getCategory(): string
    {
        return 'default';
    }

    #[Override]
    public function getName(): string
    {
        return 'form';
    }

    #[Override]
    public function isPostAction(): bool
    {
        return false;
    }

    #[Override]
    public function supports(Workflow $workflow): bool
    {
        return true;
    }

     /** {@inheritDoc} */
    #[Override]
    public function create(array $config, Transition $transition): Action
    {
        return new FormAction('action_' . $config['id'], $config['label'], $config);
    }
}
