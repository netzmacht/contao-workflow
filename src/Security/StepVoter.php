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

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Contao\User as ContaoUser;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessor;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Step;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use function assert;

/**
 * The step voter is a security voter to evaluate access to a step for a given workflow item.
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
     * @var array
     */
    private $providerConfiguration;

    /**
     * StepVoter constructor.
     *
     * @param User                  $workflowUser          The workflow user.
     * @param PropertyAccessManager $propertyAccessManager The property access manager.
     * @param array                 $providerConfiguration The provider configuration.
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
    protected function supports($attribute, $item): bool
    {
        if (! $item instanceof Item) {
            return false;
        }

        if (! $attribute instanceof Step) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute($step, $item, TokenInterface $token): bool
    {
        assert($item instanceof Item);
        assert($step instanceof Step);

        $permission        = $step->getPermission();
        $permissionLimited = $permission !== null;

        // First check is permission is limited by permission
        if ($permissionLimited && $this->workflowUser->hasPermission($permission)) {
            return true;
        }

        $providerName         = $item->getEntityId()->getProviderName();
        $assignUserProperties = ($this->providerConfiguration[$providerName]['assign_users'] ?? []);

        // If no users are assigned to, break here
        if (count($assignUserProperties) === 0) {
            return !$permissionLimited;
        }

        // Only authenticated users can be assigned
        $userId = $this->getUserId($token);
        if ($userId === null) {
            return false;
        }

        // Only entity which properties are accessible can be checked
        $accessor = $this->provideAccess($item);
        if ($accessor === null) {
            return !$permissionLimited;
        }

        if ($this->checkAssignableProperties($assignUserProperties, $accessor, $userId)) {
            return true;
        }

        return !$permissionLimited;
    }

    /**
     * Get the user id for an authenticated contao user otherwise return null.
     *
     * @param TokenInterface $token The security token.
     *
     * @return EntityId|null
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
     *
     * @return PropertyAccessor|null
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
     * @param array            $assignUserProperties List of properties used to assign users.
     * @param PropertyAccessor $accessor             The property accessor.
     * @param EntityId         $userId               The user id of the current user.
     *
     * @return bool
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
