<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Database;

use Contao\StringUtil;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateTransitionEvent;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition\ExpressionCondition;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition\PropertyCondition;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition\TransitionPermissionCondition;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as AuthorizationChecker;

/**
 * Class ConditionBuilder builds the workflow conditions.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Definition\Database
 */
final class ConditionBuilder
{
    /**
     * Comparator operations.
     *
     * @var array
     */
    private static $operators = [
        'eq'  => '==',
        'neq' => '!=',
        'lt'  => '<',
        'lte' => '<=',
        'gt'  => '>',
        'gte' => '>=',
    ];

    /**
     * The expression language.
     *
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * Authorization checker.
     *
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * ConditionBuilder constructor.
     *
     * @param ExpressionLanguage   $expressionLanguage   The expression language.
     * @param AuthorizationChecker $authorizationChecker Authorization checker.
     */
    public function __construct(ExpressionLanguage $expressionLanguage, AuthorizationChecker $authorizationChecker)
    {
        $this->expressionLanguage   = $expressionLanguage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Create property conditions.
     *
     * @param CreateTransitionEvent $event The subscribed event.
     *
     * @return void
     */
    public function createPropertyConditions(CreateTransitionEvent $event): void
    {
        $transition = $event->getTransition();

        if (!$transition->getConfigValue('addPropertyConditions')) {
            return;
        }

        $config = StringUtil::deserialize($transition->getConfigValue('propertyConditions'), true);

        foreach ($config as $row) {
            $condition = new PropertyCondition();

            if ($row['property']) {
                $condition
                    ->setProperty($row['property'])
                    ->setOperator($this->parseOperator($row['operator']))
                    ->setValue($row['value']);

                $transition->addCondition($condition);
            }
        }
    }

    /**
     * Add expression conditions.
     *
     * @param CreateTransitionEvent $event The subscribed event.
     *
     * @return void
     */
    public function createExpressionConditions(CreateTransitionEvent $event): void
    {
        $transition = $event->getTransition();

        if ($transition->getConfigValue('addExpressionConditions')) {
            $definition = StringUtil::deserialize($transition->getConfigValue('expressionConditions'), true);

            foreach ($definition as $config) {
                $condition = new ExpressionCondition($this->expressionLanguage, $config['expression']);

                if ($config['type'] === 'pre') {
                    $transition->addPreCondition($condition);
                } else {
                    $transition->addCondition($condition);
                }
            }
        }
    }

    /**
     * Add default transition conditions.
     *
     * @param CreateTransitionEvent $event The subscribed event.
     *
     * @return void
     */
    public function createTransitionPermissionCondition(CreateTransitionEvent $event): void
    {
        $transition = $event->getTransition();
        $transition->addPreCondition(
            new TransitionPermissionCondition($this->authorizationChecker, true)
        );
    }

    /**
     * Parse database stored operator into string comparison operator.
     *
     * @param string $operator Database operator.
     *
     * @return string|null
     */
    private function parseOperator($operator): ?string
    {
        return (static::$operators[$operator] ?? null);
    }
}
