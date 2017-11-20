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

namespace Netzmacht\Contao\Workflow\Definition\Database;

use Contao\StringUtil;
use Netzmacht\Contao\Workflow\Condition\Transition\ExpressionCondition;
use Netzmacht\Contao\Workflow\Condition\Transition\PropertyCondition;
use Netzmacht\Contao\Workflow\Definition\Event\CreateTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class ConditionBuilder builds the workflow conditions.
 *
 * @package Netzmacht\Contao\Workflow\Definition\Database
 */
class ConditionBuilder implements EventSubscriberInterface
{
    /**
     * The expression language.
     *
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

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
     * ConditionBuilder constructor.
     *
     * @param ExpressionLanguage $expressionLanguage The expression language.
     */
    public function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateTransitionEvent::NAME => [
                ['createPropertyConditions'],
                ['createExpressionConditions'],
            ]
        );
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
