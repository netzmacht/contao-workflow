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
     * UpdatePropertyActionFactory constructor.
     *
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

    /**
     * {@inheritDoc}
     */
    public function getCategory() : string
    {
        return 'default';
    }

    /**
     * {@inheritDoc}
     */
    public function getName() : string
    {
        return 'update_property';
    }

    /**
     * {@inheritDoc}
     */
    public function isPostAction() : bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Workflow $workflow) : bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config, Transition $transition) : Action
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
