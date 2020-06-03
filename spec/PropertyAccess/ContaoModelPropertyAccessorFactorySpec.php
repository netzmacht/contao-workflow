<?php

namespace spec\Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use Contao\Model;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\ContaoModelPropertyAccessor;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\ContaoModelPropertyAccessorFactory;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessor;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel\ContaoModelRelatedModelChangeTracker;
use PhpSpec\ObjectBehavior;

final class ContaoModelPropertyAccessorFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(new ContaoModelRelatedModelChangeTracker());
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ContaoModelPropertyAccessorFactory::class);
    }

    public function it_supports_contao_models(): void
    {
        $this->supports($this->modelInstance())->shouldReturn(true);
    }

    public function it_creates_an_property_accessor(): void
    {
        $instance = $this->create($this->modelInstance());
        $instance->shouldBeAnInstanceOf(PropertyAccessor::class);
        $instance->shouldBeAnInstanceOf(ContaoModelPropertyAccessor::class);
    }

    private function modelInstance() : Model
    {
        return new class extends Model {
            protected static $strTable = 'tl_example';

            public function __construct($objResult = null)
            {
                // Do not call parent constructor as it requires contao framework being initialized
            }
        };
    }
}
