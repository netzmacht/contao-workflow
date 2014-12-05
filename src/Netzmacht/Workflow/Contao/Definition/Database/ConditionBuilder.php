<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Definition\Database;

use Netzmacht\Workflow\Contao\Condition\Transition\ExpressionCondition;
use Netzmacht\Workflow\Contao\Condition\Transition\PropertyCondition;
use Netzmacht\Workflow\Contao\Definition\Event\CreateTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ConditionBuilder implements EventSubscriberInterface
{
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
                array('createExpressionCondition'),
            )
        );
    }

    /**
     * @param CreateTransitionEvent $event
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
     * @param CreateTransitionEvent $event
     */
    public function createExpressionCondition(CreateTransitionEvent $event)
    {
        $transition = $event->getTransition();

        if ($transition->getConfigValue('addExpressionCondition')) {
            $language  = $this->getService('workflow.transition.expression-language');
            $condition = new ExpressionCondition($language);
            $condition->setExpression($transition->getConfigValue('expressionCondition'));

            $transition->addCondition($condition);
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

    /**
     * Get service.
     *
     * @param string $name Service name
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getService($name)
    {
        return $GLOBALS['container'][$name];
    }
}

