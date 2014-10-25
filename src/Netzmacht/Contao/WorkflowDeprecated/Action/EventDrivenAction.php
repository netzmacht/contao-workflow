<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Action;


use Netzmacht\Contao\WorkflowDeprecated\Action;
use Netzmacht\Contao\WorkflowDeprecated\Action\Event\ExecuteActionEvent;
use Netzmacht\Contao\WorkflowDeprecated\Action\Event\ValidateActionEvent;
use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Netzmacht\Contao\WorkflowDeprecated\Flow\Transition;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDrivenAction implements Action
{
    /**
     * @var
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @param \ArrayObject $data
     * @return mixed
     */
    public function execute(Transition $transition, Entity $entity, \ArrayObject $data)
    {
        $event = new ExecuteActionEvent($transition, $entity, $data);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @return bool
     */
    public function validate(Transition $transition, Entity $entity)
    {
        $event = new ValidateActionEvent($transition, $entity);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->isValid();
    }

}