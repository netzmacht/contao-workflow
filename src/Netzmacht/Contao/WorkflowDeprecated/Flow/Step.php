<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Flow;


use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Netzmacht\Contao\WorkflowDeprecated\Exception\InvalidTransitionException;

class Step
{
    private $transitions = array();

    /**
     * @param Entity $entity
     * @param Transition $transition
     */
    public function transit(Entity $entity, Transition $transition)
    {
        $this->guardValidTransition($transition);
        $transition->transist($entity);

    }

    /**
     * @param Transition $transition
     * @throws InvalidTransitionException
     */
    private function guardValidTransition(Transition $transition)
    {

    }

    /**
     * @return array
     */
    public function getTransitionNames()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
    }
} 