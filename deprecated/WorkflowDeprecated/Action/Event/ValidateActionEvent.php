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


use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Netzmacht\Contao\WorkflowDeprecated\Flow\Transition;
use Symfony\Component\EventDispatcher\Event;

class ValidateActionEvent extends Event
{
    const NAME = 'workflow.action.validate';

    /**
     * @var bool
     */
    private $isValid = true;

    /**
     * @var array
     */
    private $errors  = array();

    /**
     * @var Transition
     */
    private $transition;

    /**
     * @var Entity
     */
    private $entity;

    /**
     * @param $transition
     * @param $entity
     */
    function __construct(Transition $transition, Entity $entity)
    {
        $this->transition = $transition;
        $this->entity     = $entity;
    }

    /**
     * @param $error
     */
    public function addError($error)
    {
        $this->errors[] = $error;
        $this->isValid  = false;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
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