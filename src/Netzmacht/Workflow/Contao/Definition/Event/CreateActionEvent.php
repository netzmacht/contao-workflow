<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Definition\Event;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Contao\Model\ActionModel;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class CreateActionEvent is created when an action should be created.
 *
 * @package Netzmacht\Workflow\Contao\Definition\Event
 */
class CreateActionEvent
{
    const NAME = 'workflow.factory.create-action';

    /**
     * The action model.
     *
     * @var ActionModel
     */
    private $model;

    /**
     * The created action.
     *
     * @var Action
     */
    private $action;

    /**
     * Workflow the action belongs to.
     *
     * @var Workflow
     */
    private $workflow;

    /**
     * Construct.
     *
     * @param Workflow    $workflow Current workflow.
     * @param ActionModel $model    Action model.
     */
    public function __construct(Workflow $workflow, ActionModel $model)
    {
        $this->workflow = $workflow;
        $this->model    = $model;
    }

    /**
     * Get the action model.
     *
     * @return ActionModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * Get the action.
     *
     * @return Action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the action.
     *
     * @param Action $action The action being created.
     *
     * @return $this
     */
    public function setAction(Action $action)
    {
        $this->action = $action;

        return $this;
    }
}
