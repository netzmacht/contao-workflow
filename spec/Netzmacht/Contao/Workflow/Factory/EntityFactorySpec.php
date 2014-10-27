<?php

namespace spec\Netzmacht\Contao\Workflow\Factory;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use Database\Result;
use Netzmacht\Contao\Workflow\Factory\EntityFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class_alias('spec\Netzmacht\Contao\Workflow\Factory\Model', 'Model');

/**
 * Class EntityFactorySpec
 * @package spec\Netzmacht\Contao\Workflow\Factory
 * @mixin EntityFactory
 */
class EntityFactorySpec extends ObjectBehavior
{
    const ENTITY = 'Netzmacht\Contao\Workflow\Entity\Entity';

    private $row;

    function let()
    {
        $this->row =  array(
            'id'   => 1,
            'name' => 'Test'
        );

    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Factory\EntityFactory');
    }

    function it_creates_entity_from_array()
    {

        $entity = $this->createFromArray($this->row, 'tl_test');

        $entity->shouldBeAnInstanceOf(static::ENTITY);
        $entity->getProperty('name')->shouldReturn('Test');
        $entity->getID()->shouldReturn(1);
    }

    function it_creates_entity_from_result(Result $result)
    {
        $result->row()->willReturn($this->row);

        $entity = $this->createFromResult($result, 'tl_test');

        $entity->shouldBeAnInstanceOf(static::ENTITY);
        $entity->getProperty('name')->shouldReturn('Test');
        $entity->getID()->shouldReturn(1);
    }

    function it_creates_entity_from_dc_general_model(ModelInterface $dcGeneralModel)
    {
        $dcGeneralModel->getId()->willReturn(1);
        $dcGeneralModel->getProperty('name')->willReturn('Test');

        $entity = $this->createFromDcGeneralModel($dcGeneralModel);

        $entity->shouldBeAnInstanceOf(static::ENTITY);
        $entity->getProperty('name')->shouldReturn('Test');
        $entity->getID()->shouldReturn(1);
    }

    function it_creates_entity_from_contao_model(\Model $contaoModel)
    {
        $contaoModel->id = 1;
        $contaoModel->name = 'Test';
        $contaoModel->getPk()->willReturn('id');

        $entity = $this->createFromContaoModel($contaoModel);

        $entity->shouldBeAnInstanceOf(static::ENTITY);
        $entity->getProperty('name')->shouldReturn('Test');
        $entity->getID()->shouldReturn(1);
    }
}

class Model
{
    public $id;
    public $name;

    public function getPk()
    {
        return 'id';
    }
}
