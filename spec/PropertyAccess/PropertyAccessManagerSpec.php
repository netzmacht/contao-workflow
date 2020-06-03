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

use Assert\InvalidArgumentException;
use Netzmacht\ContaoWorkflowBundle\Exception\PropertyAccessFailed;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessor;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessorFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class PropertyAccessManagerSpec extends ObjectBehavior
{
    public function let(PropertyAccessorFactory $unsupportedFactory, PropertyAccessorFactory $factory): void
    {
        $unsupportedFactory->supports(Argument::any())->willReturn(false);
        $factory->supports(Argument::any())->willReturn(true);

        $this->beConstructedWith([$unsupportedFactory, $factory]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PropertyAccessManager::class);
    }

    public function it_expects_property_accessor_factories(): void
    {
        $this->beConstructedWith([[], (object) []]);
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_asks_factories_for_support(
        PropertyAccessorFactory $unsupportedFactory,
        PropertyAccessorFactory $factory
    ): void {
        $unsupportedFactory->supports(Argument::any())->shouldBeCalledOnce();
        $factory->supports(Argument::any())->shouldBeCalledOnce();

        $this->supports([])->shouldReturn(true);
    }

    public function it_provides_access_using_supported_factory(
        PropertyAccessorFactory $factory,
        PropertyAccessor $accessor
    ): void {
        $factory->create([])->willReturn($accessor);
        $this->provideAccess([])->shouldReturn($accessor);
    }

    public function it_throws_property_access_failed_for_unsupported_data(PropertyAccessorFactory $unsupportedFactory
    ): void {
        $this->beConstructedWith([$unsupportedFactory]);
        $this->shouldThrow(PropertyAccessFailed::class)->during('provideAccess', [[]]);
    }
}
