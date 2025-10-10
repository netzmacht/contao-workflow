<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Override;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * ExpressionCondition uses the symfony expression syntax for defining conditions.
 */
final class ExpressionCondition implements Condition
{
    /**
     * The expression language. The language is injected by dependency so that features can be added.
     */
    private ExpressionLanguage $expressionLanguage;

    /**
     * Plain expression.
     */
    private string $expression;

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
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    #[Override]
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $values = [
            'transition' => $transition,
            'item'       => $item,
            'context'    => $context,
            'entity'     => $item->getEntity(),
            'entityId'   => $item->getEntityId(),
        ];

        if ($this->expressionLanguage->evaluate($this->expression, $values)) {
            return true;
        }

        $context->addError('transition.condition.expression.failed', ['%expression%' => $this->expression]);

        return false;
    }
}
