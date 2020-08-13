<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\UpdateEntityAction;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractPropertyAccessAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use function array_key_exists;
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

    /**
     * {@inheritDoc}
     */
    public function validate(Item $item, Context $context): bool
    {
        $payload = $context->getPayload();
        if ($payload === null) {
            return false;
        }

        if (!isset($payload[$this->getName()])) {
            return false;
        }

        return is_array($payload[$this->getName()]);
    }

    /**
     * {@inheritDoc}
     */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $payload = $context->getPayload();
        $name    = $this->getName();
        assert(is_array($payload));
        assert(array_key_exists($name, $payload));
        assert(is_array($payload[$name]));

        $accessor = $this->propertyAccessManager->provideAccess($item->getEntity());
        foreach ($payload[$name] as $key => $value) {
            $accessor->set($key, $value);
        }
    }
}
