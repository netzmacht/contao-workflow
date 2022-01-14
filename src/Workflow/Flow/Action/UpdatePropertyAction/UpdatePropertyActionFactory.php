<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\UpdatePropertyAction;

use Assert\Assert;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Security\User;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Factory to create a new instance of the update property action.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class UpdatePropertyActionFactory implements ActionTypeFactory
{
    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * Symfony expression language.
     *
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */    private $repositoryManager;

    /**
     * Workflow user.
     *
     * @var User
     */
    private $user;

    /**
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param ExpressionLanguage    $expressionLanguage    Expression language.
     * @param RepositoryManager     $repositoryManager     Repository manager.
     * @param User                  $user                  Workflow user.
     */
    public function __construct(
        PropertyAccessManager $propertyAccessManager,
        ExpressionLanguage $expressionLanguage,
        RepositoryManager $repositoryManager,
        User $user
    ) {
        $this->propertyAccessManager = $propertyAccessManager;
        $this->expressionLanguage    = $expressionLanguage;
        $this->repositoryManager     = $repositoryManager;
        $this->user                  = $user;
    }

    public function getCategory(): string
    {
        return 'default';
    }

    public function getName(): string
    {
        return 'update_property';
    }

    public function isPostAction(): bool
    {
        return false;
    }

    public function supports(Workflow $workflow): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config, Transition $transition): Action
    {
        Assert::that($config)
            ->keyExists('property')
            ->keyExists('property_value')
            ->keyExists('property_expression');

        return new UpdatePropertyAction(
            $config['property'],
            (string) $config['property_value'],
            (bool) $config['property_expression'],
            $this->propertyAccessManager,
            $this->expressionLanguage,
            $this->repositoryManager,
            $this->user
        );
    }
}
