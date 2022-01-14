<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Contao\User as ContaoUser;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessor;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Step;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

use function assert;
use function count;

/**
 * The step voter is a security voter to evaluate access to a step for a given workflow item.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class StepVoter extends Voter
{
    /**
     * The workflow user.
     *
     * @var User
     */
    private $workflowUser;

    /**
     * The property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * The provider configuration.
     *
     * @var array<string,array<string,mixed>>
     */
    private $providerConfiguration;

    /**
     * @param User                              $workflowUser          The workflow user.
     * @param PropertyAccessManager             $propertyAccessManager The property access manager.
     * @param array<string,array<string,mixed>> $providerConfiguration The provider configuration.
     */
    public function __construct(
        User $workflowUser,
        PropertyAccessManager $propertyAccessManager,
        array $providerConfiguration
    ) {
        $this->workflowUser          = $workflowUser;
        $this->propertyAccessManager = $propertyAccessManager;
        $this->providerConfiguration = $providerConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($attribute, $subject): bool
    {
        if (! $subject instanceof Item) {
            return false;
        }

        return $attribute instanceof Step;
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @psalm-suppress DocblockTypeContradiction - TODO: Do we need to fix the attribute type */
        assert($attribute instanceof Step);
        assert($subject instanceof Item);

        $permission        = $attribute->getPermission();
        $permissionLimited = $permission !== null;
        $user              = $token->getUser();
        if (! $user instanceof UserInterface) {
            $user = null;
        }

        // First check is permission is limited by permission
        if ($permissionLimited && $this->workflowUser->hasPermission($permission, $user)) {
            return true;
        }

        $providerName         = $subject->getEntityId()->getProviderName();
        $assignUserProperties = ($this->providerConfiguration[$providerName]['assign_users'] ?? []);

        // If no users are assigned to, break here
        if (count($assignUserProperties) === 0) {
            return ! $permissionLimited;
        }

        // Only authenticated users can be assigned
        $userId = $this->getUserId($token);
        if ($userId === null) {
            return false;
        }

        // Only entity which properties are accessible can be checked
        $accessor = $this->provideAccess($subject);
        if ($accessor === null) {
            return ! $permissionLimited;
        }

        if ($this->checkAssignableProperties($assignUserProperties, $accessor, $userId)) {
            return true;
        }

        return ! $permissionLimited;
    }

    /**
     * Get the user id for an authenticated contao user otherwise return null.
     *
     * @param TokenInterface $token The security token.
     */
    private function getUserId(TokenInterface $token): ?EntityId
    {
        $user = $token->getUser();
        if (! $user instanceof ContaoUser) {
            return null;
        }

        return $this->workflowUser->getUserId($user);
    }

    /**
     * Check if entity supports property access and create property accessor for it.
     *
     * @param Item $item The current item.
     */
    private function provideAccess(Item $item): ?PropertyAccessor
    {
        $entity = $item->getEntity();
        if (! $this->propertyAccessManager->supports($entity)) {
            return null;
        }

        return $this->propertyAccessManager->provideAccess($entity);
    }

    /**
     * Check each assign user property until first match is found.
     *
     * @param list<string>     $assignUserProperties List of properties used to assign users.
     * @param PropertyAccessor $accessor             The property accessor.
     * @param EntityId         $userId               The user id of the current user.
     */
    protected function checkAssignableProperties(
        array $assignUserProperties,
        PropertyAccessor $accessor,
        EntityId $userId
    ): bool {
        foreach ($assignUserProperties as $property) {
            $value = $accessor->get($property);
            if ($value === null) {
                continue;
            }

            if ($userId->equals(EntityId::fromString($value))) {
                return true;
            }
        }

        return false;
    }
}
