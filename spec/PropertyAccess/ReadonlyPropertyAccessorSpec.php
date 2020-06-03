<?php

namespace spec\Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessor;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\ReadonlyPropertyAccessor;
use PhpSpec\ObjectBehavior;
use function serialize;

final class ReadonlyPropertyAccessorSpec extends ObjectBehavior
{
    public function let(PropertyAccessor $propertyAccessor): void
    {
        $this->beConstructedWith($propertyAccessor);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ReadonlyPropertyAccessor::class);
    }

    public function it_delegates_has(PropertyAccessor $propertyAccessor): void
    {
        $propertyAccessor->has('foo')->shouldBeCalledOnce();
        $this->has('foo');
    }

    public function it_provides_raw_access(PropertyAccessor $propertyAccessor): void
    {
        $propertyAccessor->get('foo')->willReturn(serialize(['bar', 'baz']));
        $this->raw('foo')->shouldReturn(serialize(['bar', 'baz']));
    }

    public function it_deserialize_raw_values(PropertyAccessor $propertyAccessor): void
    {
        $propertyAccessor->get('foo')->willReturn(serialize(['bar', 'baz']));
        $this->get('foo')->shouldReturn(['bar', 'baz']);
    }
}
