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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Metadata;

use Netzmacht\ContaoWorkflowBundle\Security\User;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class MetadataAction
 */
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
     * TrackUserAction constructor.
     *
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

    /**
     * {@inheritDoc}
     */
    public function validate(Item $item, Context $context): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $userId   = $this->user->getUserId();
        $metadata = [
            'scope'  => null,
            'userId' => $userId ? (string) $userId : null,
        ];

        if ($this->requestStack->getCurrentRequest()) {
            $metadata['scope'] = $this->requestStack->getCurrentRequest()->attributes->get('_scope');
        }

        $context->getProperties()->set('metadata', $metadata);
    }
}
