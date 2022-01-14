<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Metadata;

use Netzmacht\ContaoWorkflowBundle\Security\User;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\HttpFoundation\RequestStack;

final class MetadataAction implements Action
{
    /**
     * The workflow user.
     *
     * @var User
     */
    private $user;

    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param User         $user         The workflow user.
     * @param RequestStack $requestStack The request stack.
     */
    public function __construct(User $user, RequestStack $requestStack)
    {
        $this->user         = $user;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        return [];
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function validate(Item $item, Context $context): bool
    {
        return true;
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $userId   = $this->user->getUserId();
        $metadata = [
            'scope'  => null,
            'userId' => $userId ? (string) $userId : null,
        ];

        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $metadata['scope'] = $request->attributes->get('_scope');
        }

        $context->getProperties()->set('metadata', $metadata);
    }
}
