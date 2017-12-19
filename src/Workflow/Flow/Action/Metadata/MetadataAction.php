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

use Contao\BackendUser;
use Contao\FrontendUser;
use Contao\User;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface as TokenStorage;

/**
 * Class MetadataAction
 */
class MetadataAction implements Action
{
    /**
     * The token storage.
     *
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * TrackUserAction constructor.
     *
     * @param TokenStorage $tokenStorage
     * @param RequestStack $requestStack
     */
    public function __construct(TokenStorage $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
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
        $token    = $this->tokenStorage->getToken();
        $metadata = [
            'scope'  => null,
            'userId' => null,
        ];

        if ($this->requestStack->getCurrentRequest()) {
            $metadata['scope'] = $this->requestStack->getCurrentRequest()->attributes->get('_scope');
        }

        if ($token && ($user = $token->getUser()) instanceof User) {
            if ($user instanceof FrontendUser) {
                $metadata['userId'] = (string) EntityId::fromProviderNameAndId('tl_member', $user->id);
            }

            if ($user instanceof BackendUser) {
                $metadata['userId'] = (string) EntityId::fromProviderNameAndId('tl_user', $user->id);
            }
        }

        $context->getProperties()->set('metadata', $metadata);
    }
}
