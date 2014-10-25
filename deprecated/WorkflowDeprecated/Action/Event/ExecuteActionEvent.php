<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Action\Event;


use ArrayObject;
use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Netzmacht\Contao\WorkflowDeprecated\Flow\Transition;
use Symfony\Component\EventDispatcher\Event;

class ExecuteActionEvent extends Event
{
    const NAME = 'workflow.action.execute';

    /**
     * @var Transition
     */
    private $transition;

    /**
     * @var Entity
     */
    private $entity;

    /**
     * @var ArrayObject
     */
    private $data;

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @param ArrayObject $data
     */
    function __construct(Transition $transition, Entity $entity, ArrayObject $data)
    {
        $this->transition = $transition;
        $this->entity     = $entity;
        $this->data       = $data;
    }

    /**
     * @return ArrayObject
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return Transition
     */
    public function getTransition()
    {
        return $this->transition;
    }
} 