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

namespace Netzmacht\ContaoWorkflowBundle\Definition\Database;

use Contao\StringUtil;
use Netzmacht\ContaoWorkflowBundle\Definition\Event\CreateTransitionEvent;
use Netzmacht\ContaoWorkflowBundle\Flow\Condition\Transition\ExpressionCondition;
use Netzmacht\ContaoWorkflowBundle\Flow\Condition\Transition\PropertyCondition;
use Netzmacht\ContaoWorkflowBundle\Flow\Condition\Transition\TransitionPermissionCondition;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as AuthorizationChecker;

/**
 * Class ConditionBuilder builds the workflow conditions.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Definition\Database
 */
class ConditionBuilder
{
    /**
     * Comparator operations.
     *
     * @var array
     */
    protected static $operators = array(
        'eq'  => '==',
        'neq' => '!=',
        'lt'  => '<',
        'lte' => '<=',
        'gt'  => '>',
        'gte' => '>='
    );

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
    public function createPropertyConditions(CreateTransitionEvent $event)
    {
        $transition = $event->getTransition();

        if ($transition->getConfigValue('addPropertyConditions')) {
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
    }

    /**
     * Add expression conditions.
     *
     * @param CreateTransitionEvent $event The subscribed event.
     *
     * @return void
     */
    public function createExpressionConditions(CreateTransitionEvent $event)
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
    public function createTransitionPermissionCondition(CreateTransitionEvent $event)
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
    private function parseOperator($operator)
    {
        if (isset(static::$operators[$operator])) {
            return static::$operators[$operator];
        }

        return null;
    }
}
