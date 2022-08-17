<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\UpdateEntityAction;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractPropertyAccessAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

use function assert;
use function is_array;

/**
 * The update entity action
 */
final class UpdateEntityAction extends AbstractPropertyAccessAction
{
    /**
     * {@inheritDoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        return [$this->getName()];
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function validate(Item $item, Context $context): bool
    {
        $payload = $context->getPayload();
        if (! $payload->has($this->getName())) {
            return false;
        }

        return is_array($payload->get($this->getName()));
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $payload = $context->getPayload();
        $name    = $this->getName();
        assert($payload->has($name));

        $data = $payload->get($name);
        assert(is_array($data));

        $accessor = $this->propertyAccessManager->provideAccess($item->getEntity());
        foreach ($data as $key => $value) {
            $accessor->set($key, $value);
        }
    }
}
