<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Condition\Transition;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionCondition implements Condition
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * Plain expression
     *
     * @var string
     */
    private $expression;

    /**
     * @var string
     */
    private $message = 'transition.condition.expression.failed';

    /**
     * Compiled expression
     *
     * @var string
     */
    private $compiled;

    /**
     * Construct.
     *
     * @param ExpressionLanguage $expressionLanguage The expression language.
     */
    function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * @param $expression
     *
     * @return $this
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
        $this->compiled   = 'return ' . $this->expressionLanguage->compile(
            $expression,
            array('transition', 'item', 'entity', 'entityId', 'context', 'errorCollection')
        ) . ';';

        return $this;
    }

    /**
     * Get compiled expression.
     *
     * @return mixed
     */
    public function getCompiled()
    {
        return $this->compiled;
    }

    /**
     * Get expression.
     *
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    public function match(Transition $transition, Item $item, Context $context, ErrorCollection $errorCollection)
    {
        if (!$this->compiled) {
            return false;
        }

        // Vars are created so that eval can access them.
        $entity   = $item->getEntity();
        $entityId = $item->getEntityId();

        if (eval($this->compiled)) {
            return true;
        }

        $errorCollection->addError($this->message, array($this->expression));
        return false;
    }
}
