<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Database;

use Contao\StringUtil;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateTransitionEvent;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition\ExpressionCondition;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition\PropertyCondition;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition\TransitionPermissionCondition;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as AuthorizationChecker;

use function is_string;

/**
 * Class ConditionBuilder builds the workflow conditions.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class ConditionBuilder
{
    /**
     * Comparator operations.
     *
     * @var array<string,string>
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
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * @param ExpressionLanguage    $expressionLanguage    The expression language.
     * @param AuthorizationChecker  $authorizationChecker  Authorization checker.
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     */
    public function __construct(
        ExpressionLanguage $expressionLanguage,
        AuthorizationChecker $authorizationChecker,
        PropertyAccessManager $propertyAccessManager
    ) {
        $this->expressionLanguage    = $expressionLanguage;
        $this->authorizationChecker  = $authorizationChecker;
        $this->propertyAccessManager = $propertyAccessManager;
    }

    /**
     * Create property conditions.
     *
     * @param CreateTransitionEvent $event The subscribed event.
     */
    public function createPropertyConditions(CreateTransitionEvent $event): void
    {
        $transition = $event->getTransition();

        if (! $transition->getConfigValue('addPropertyConditions')) {
            return;
        }

        $config = StringUtil::deserialize($transition->getConfigValue('propertyConditions'), true);

        foreach ($config as $row) {
            $condition = new PropertyCondition($this->propertyAccessManager);

            if (! $row['property'] || ! is_string($row['operator'])) {
                continue;
            }

            $operator = $this->parseOperator($row['operator']);
            if ($operator === null) {
                continue;
            }

            $condition
                ->setProperty($row['property'])
                ->setOperator($operator)
                ->setValue($row['value']);

            $transition->addCondition($condition);
        }
    }

    /**
     * Add expression conditions.
     *
     * @param CreateTransitionEvent $event The subscribed event.
     */
    public function createExpressionConditions(CreateTransitionEvent $event): void
    {
        $transition = $event->getTransition();

        if (! $transition->getConfigValue('addExpressionConditions')) {
            return;
        }

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

    /**
     * Add default transition conditions.
     *
     * @param CreateTransitionEvent $event The subscribed event.
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
     */
    private function parseOperator(string $operator): ?string
    {
        return self::$operators[$operator] ?? null;
    }
}
