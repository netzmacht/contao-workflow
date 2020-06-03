<?php

namespace spec\Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use Contao\Model;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\ContaoModelPropertyAccessor;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessor;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel\ContaoModelRelatedModelChangeTracker;
use PhpSpec\ObjectBehavior;
use function expect;

final class ContaoModelPropertyAccessorSpec extends ObjectBehavior
{
    /** @var ContaoModelRelatedModelChangeTracker */
    private $changeTracker;

    /** @var Model */
    private $model;

    public function let(): void
    {
        $this->changeTracker = new ContaoModelRelatedModelChangeTracker();
        $this->model         = $this->modelInstance(['foo' => 'bar']);

        $this->beConstructedWith($this->model, $this->changeTracker);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ContaoModelPropertyAccessor::class);
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

    public function it_provides_access_to_related_models(): void
    {
        $this->has('parent.id')->shouldReturn(true);
        $this->get('parent.id')->shouldReturn(12);

        $this->has('parent.foo')->shouldReturn(false);
        $this->set('parent.foo', true);
        $this->has('parent.foo')->shouldReturn(true);
        $this->get('parent.foo')->shouldReturn(true);
    }

    public function it_tracks_changed_related_models(): void
    {
        expect($this->changeTracker->release($this->model))->shouldBe([]);
        $this->set('parent.foo', true);
        expect($this->changeTracker->release($this->model))->shouldHaveCount(1);
    }

    private function modelInstance(array $data = []) : Model
    {
        $model = new class extends Model {
            protected static $strTable = 'tl_example';

            public function __construct($objResult = null)
            {
                // Do not call parent constructor as it requires contao framework being initialized
            }

            public function getRelated($strKey, array $arrOptions = [])
            {
                static $instance;
                if ($strKey === 'parent') {
                    if ($instance === null) {
                        $instance = new class extends Model {
                            protected static $strTable = 'tl_example_parent';

                            public function __construct($objResult = null)
                            {
                                // Do not call parent constructor as it requires contao framework being initialized
                            }
                        };

                        $instance->setRow(['id' => 12]);
                    }

                    return $instance;
                }

                throw new \Exception("Field $strKey does not seem to be related");
            }
        };

        $model->setRow($data);

        return $model;
    }
}
