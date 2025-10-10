<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\UpdatePropertyAction;

use Contao\MemberModel;
use Contao\Model;
use Contao\UserModel;
use DateTimeImmutable;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessor;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\ReadonlyPropertyAccessor;
use Netzmacht\ContaoWorkflowBundle\Security\User;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractPropertyAccessAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Update a property action
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class UpdatePropertyAction extends AbstractPropertyAccessAction
{
    /**
     * The name of the property.
     */
    private string $property;

    /**
     * The new value. Might be an an symfony expression.
     */
    private mixed $value;

    /**
     * Symfony expression language.
     */
    private ExpressionLanguage $expressionLanguage;

    /**
     * Value is an expression which has to be evaluated.
     */
    private bool $isExpression;

    /**
     * Repository manager.
     */
    private RepositoryManager $repositoryManager;

    /**
     * Workflow user.
     */
    private User $user;

    /**
     * Construct.
     *
     * @param string                $property              The property to be changed.
     * @param mixed                 $value                 The value to be adjusted.
     * @param bool                  $isExpression          Value is an expression which has to be evaluated.
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param ExpressionLanguage    $expressionLanguage    The expression language.
     * @param RepositoryManager     $repositoryManager     Repository manager.
     * @param User                  $user                  Workflow user.
     */
    public function __construct(
        string $property,
        $value,
        bool $isExpression,
        PropertyAccessManager $propertyAccessManager,
        ExpressionLanguage $expressionLanguage,
        RepositoryManager $repositoryManager,
        User $user,
    ) {
        parent::__construct($propertyAccessManager, 'Update property action');

        $this->property           = $property;
        $this->value              = $value;
        $this->isExpression       = $isExpression;
        $this->expressionLanguage = $expressionLanguage;
        $this->repositoryManager  = $repositoryManager;
        $this->user               = $user;
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
        $entity = $item->getEntity();

        return $this->propertyAccessManager->supports($entity);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $entity           = $item->getEntity();
        $propertyAccessor = $this->propertyAccessManager->provideAccess($entity);
        $propertyAccessor->set($this->property, $this->evaluateValue($propertyAccessor));
    }

    /**
     * Evaluate given expression.
     *
     * @param PropertyAccessor $propertyAccessor The property accessor.
     */
    private function evaluateValue(PropertyAccessor $propertyAccessor): mixed
    {
        if (! $this->isExpression) {
            return $this->value;
        }

        return $this->expressionLanguage->evaluate(
            $this->value,
            [
                'entity' => new ReadonlyPropertyAccessor($propertyAccessor),
                'now'    => new DateTimeImmutable(),
                'userId' => $this->user->getUserId(),
                'user'   => $this->fetchUserModel(),
            ],
        );
    }

    /**
     * @return UserModel|MemberModel|null
     *
     * @psalm-suppress MoreSpecificReturnType
     */
    private function fetchUserModel(): Model|null
    {
        $userId = $this->user->getUserId();
        if ($userId === null) {
            return null;
        }

        /** @psalm-var class-string<Model> */
        $tableName  = Model::getClassFromTable($userId->getProviderName());
        $repository = $this->repositoryManager->getRepository($tableName);

        /** @psalm-suppress LessSpecificReturnStatement */
        return $repository->find((int) $userId->getIdentifier());
    }
}
