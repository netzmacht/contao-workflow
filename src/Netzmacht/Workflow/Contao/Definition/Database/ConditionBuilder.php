<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Workflow\Contao\Definition\Database;

use Netzmacht\Workflow\Contao\Condition\Transition\ExpressionCondition;
use Netzmacht\Workflow\Contao\Condition\Transition\PropertyCondition;
use Netzmacht\Workflow\Contao\Definition\Event\CreateTransitionEvent;
use Netzmacht\Workflow\Contao\ServiceContainerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ConditionBuilder builds the workflow conditions.
 *
 * @package Netzmacht\Workflow\Contao\Definition\Database
 */
class ConditionBuilder implements EventSubscriberInterface
{
    use ServiceContainerTrait;

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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateTransitionEvent::NAME => array(
                array('createPropertyConditions'),
                array('createExpressionConditions'),
            )
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
            $config = deserialize($transition->getConfigValue('propertyConditions'), true);

            foreach ($config as $row) {
                $condition = new PropertyCondition();

                if ($row['property']) {
                    $condition
                        ->setProperty($row['property'])
                        ->setOperator($this->parseOperator($row['operator']))
                        ->getValue($row['value']);

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
            $language   = $this->getService('workflow.transition.expression-language');
            $definition = deserialize($transition->getConfigValue('expressionConditions'), true);

            foreach ($definition as $config) {
                $condition = new ExpressionCondition($language);
                $condition->setExpression($config['expression']);

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
