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
 */
final class UpdatePropertyAction extends AbstractPropertyAccessAction
{
    /**
     * The name of the property.
     *
     * @var string
     */
    private $property;

    /**
     * The new value. Might be an an symfony expression.
     *
     * @var mixed
     */
    private $value;

    /**
     * Symfony expression language.
     *
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * Value is an expression which has to be evaluated.
     *
     * @var bool
     */
    private $isExpression;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Workflow user.
     *
     * @var User
     */
    private $user;

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
        User $user
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
    public function getRequiredPayloadProperties(Item $item) : array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function validate(Item $item, Context $context) : bool
    {
        $entity = $item->getEntity();
        if ($this->propertyAccessManager->supports($entity)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function transit(Transition $transition, Item $item, Context $context) : void
    {
        $entity           = $item->getEntity();
        $propertyAccessor = $this->propertyAccessManager->provideAccess($entity);
        $propertyAccessor->set($this->property, $this->evaluateValue($propertyAccessor));
    }

    /**
     * Evaluate given expression.
     *
     * @param PropertyAccessor $propertyAccessor The property accessor.
     *
     * @return mixed
     */
    private function evaluateValue(PropertyAccessor $propertyAccessor)
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
                'user'   => $this->fetchUserModel()
            ]
        );
    }

    /** @return UserModel|MemberModel|null */
    private function fetchUserModel(): ?Model
    {
        $userId = $this->user->getUserId();
        if ($userId === null) {
            return null;
        }

        $repository = $this->repositoryManager->getRepository(Model::getClassFromTable($userId->getProviderName()));

        return $repository->find((int) $userId->getIdentifier());
    }
}
