<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Notification;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Contracts\EventDispatcher\Event;

final class BuildNotificationTokensEvent extends Event
{
    /**
     * Current workflow transition being processed.
     *
     * @var Transition
     */
    private $transition;

    /**
     * The workflow item.
     *
     * @var Item
     */
    private $item;

    /**
     * The transition context.
     *
     * @var Context
     */
    private $context;

    /**
     * The generated tokens.
     *
     * @var array<string,mixed>
     */
    private $tokens;

    /**
     * @param Transition          $transition Current workflow transition being processed.
     * @param Item                $item       The workflow item.
     * @param Context             $context    The transition context.
     * @param array<string,mixed> $tokens     The generated tokens.
     */
    public function __construct(Transition $transition, Item $item, Context $context, array $tokens)
    {
        $this->transition = $transition;
        $this->item       = $item;
        $this->context    = $context;
        $this->tokens     = $tokens;
    }

    /**
     * Get the workflow transition.
     */
    public function getTransition(): Transition
    {
        return $this->transition;
    }

    /**
     * Get the workflow item.
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * Get the current context.
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * Get the tokens.
     *
     * @return array<string,mixed>
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * Add token.
     *
     * @param string $key   The token key.
     * @param mixed  $value The token value.
     */
    public function addToken(string $key, $value): void
    {
        $this->tokens[$key] = $value;
    }

    /**
     * Add set of tokens.
     *
     * @param array<string, mixed> $tokens Set of tokens.
     */
    public function addTokens(array $tokens): void
    {
        foreach ($tokens as $key => $value) {
            $this->addToken($key, $value);
        }
    }
}
