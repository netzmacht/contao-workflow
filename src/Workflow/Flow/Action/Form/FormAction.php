<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Form;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Override;

final class FormAction extends AbstractAction
{
    /**
     * {@inheritDoc}
     */
    #[Override]
    public function getRequiredPayloadProperties(Item $item): array
    {
        return [$this->getName()];
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    #[Override]
    public function validate(Item $item, Context $context): bool
    {
        return true;
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    #[Override]
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $context->getProperties()->set($this->getName(), $context->getPayload()->get($this->getName()));
    }
}
