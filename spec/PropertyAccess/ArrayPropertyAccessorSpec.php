<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace spec\Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\ArrayPropertyAccessor;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessor;
use PhpSpec\ObjectBehavior;

final class ArrayPropertyAccessorSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(['foo' => 'bar']);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ArrayPropertyAccessor::class);
    }

    public function it_is_a_property_accessor(): void
    {
        $this->shouldImplement(PropertyAccessor::class);
    }

    public function it_provides_property_access(): void
    {
        $this->has('foo')->shouldReturn(true);
        $this->get('foo')->shouldReturn('bar');
    }

    public function it_uses_null_as_fallback(): void
    {
        $this->has('baz')->shouldReturn(false);
        $this->get('baz')->shouldReturn(null);
    }

    public function it_provides_write_access(): void
    {
        $this->has('baz')->shouldReturn(false);
        $this->set('baz', true);
        $this->has('baz')->shouldReturn(true);
        $this->get('baz')->shouldReturn(true);
    }
}
