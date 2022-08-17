<?php

declare(strict_types=1);

namespace spec\Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Notification;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Notification\BuildNotificationTokensEvent;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;

final class BuildNotificationTokensEventSpec extends ObjectBehavior
{
    /** @var string[] */
    private static $tokens = ['foo' => 'bar'];

    public function let(Transition $transition, Item $item, Context $context): void
    {
        $this->beConstructedWith($transition, $item, $context, self::$tokens);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BuildNotificationTokensEvent::class);
    }

    public function it_knows_the_transition(Transition $transition): void
    {
        $this->getTransition()->shouldReturn($transition);
    }

    public function it_knows_the_item(Item $item): void
    {
        $this->getItem()->shouldReturn($item);
    }

    public function it_knows_the_context(Context $context): void
    {
        $this->getContext()->shouldReturn($context);
    }

    public function it_knows_the_current_tokens(): void
    {
        $this->getTokens()->shouldReturn(self::$tokens);
    }

    public function it_adds_a_token(): void
    {
        $this->addToken('baz', 'example');
        $this->getTokens()->shouldHaveCount(2);
        $this->getTokens()->shouldHaveKeyWithValue('baz', 'example');
    }

    public function it_overrides_existing_token(): void
    {
        $this->getTokens()->shouldHaveKeyWithValue('foo', 'bar');
        $this->addToken('foo', 'example');
        $this->getTokens()->shouldHaveCount(1);
        $this->getTokens()->shouldHaveKeyWithValue('foo', 'example');
    }

    public function it_adds_a_set_of_tokens(): void
    {
        $this->addTokens(
            [
                'example' => 1,
                'baz'     => 2,
            ]
        );

        $this->getTokens()->shouldHaveKeyWithValue('example', 1);
        $this->getTokens()->shouldHaveKeyWithValue('baz', 2);
    }
}
