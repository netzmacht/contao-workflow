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
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
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
     * UpdatePropertyActionFactory constructor.
     *
     * @param PropertyAccessManager $propertyAccessManager
     */
    public function __construct(PropertyAccessManager $propertyAccessManager)
    {
        $this->propertyAccessManager = $propertyAccessManager;
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
            $this->createExpressionLanguage()
        );
    }

    /**
     * Create the expression language.
     *
     * @return ExpressionLanguage
     */
    private function createExpressionLanguage() : ExpressionLanguage
    {
        static $expressionLanguage;

        if ($expressionLanguage === null) {
            $expressionLanguage = new ExpressionLanguage();
            $expressionLanguage->register(
                'constant',
                // @codingStandardsIgnoreStart
                static function () {
                    return "throw new \\InvalidArgumentException('Cannot use the constant() function in the expression' 
                        . ' for security reasons.');";
                },
                static function () {
                    throw new \InvalidArgumentException(
                        'Cannot use the constant() function in the expression for security reasons.'
                    );
                }
                // @codingStandardsIgnoreEnd
            );
        }

        return $expressionLanguage;
    }
}
