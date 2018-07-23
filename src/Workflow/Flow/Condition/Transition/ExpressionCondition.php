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

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * ExpressionCondition uses the symfony expression syntax for defining conditions.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Condition\Transition
 */
final class ExpressionCondition implements Condition
{
    /**
     * The expression language. The language is injected by dependency so that features can be added.
     *
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * Plain expression.
     *
     * @var string
     */
    private $expression;

    /**
     * Construct.
     *
     * @param ExpressionLanguage $expressionLanguage The expression language.
     * @param string             $expression         The expression.
     */
    public function __construct(ExpressionLanguage $expressionLanguage, string $expression)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->expression         = $expression;
    }

    /**
     * Get expression.
     *
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $values = [
            'transition' => $transition,
            'item'       => $item,
            'context'    => $context,
            'entity'     => $item->getEntity(),
            'entityId'   => $item->getEntityId()
        ];

        if ($this->expressionLanguage->evaluate($this->expression, $values)) {
            return true;
        }

        $context->addError('transition.condition.expression.failed', [$this->expression]);

        return false;
    }
}
