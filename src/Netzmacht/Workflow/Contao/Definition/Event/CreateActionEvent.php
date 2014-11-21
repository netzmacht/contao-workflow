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
use Netzmacht\Workflow\Flow\Transition;
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
     * The action configuration.
     *
     * @var mixed
     */
    private $config;

    /**
     * The config source.
     *
     * @var null|string
     */
    private $configSource;

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
     * Workflow transition.
     *
     * @var Transition
     */
    private $transition;

    /**
     * Construct.
     *
     * @param Workflow    $workflow     Current workflow.
     * @param Transition  $transition   Current transition.
     * @param mixed       $config       Action configuration.
     * @param string|null $configSource Config source.
     */
    public function __construct(Workflow $workflow, Transition $transition, $config, $configSource = null)
    {
        $this->workflow     = $workflow;
        $this->config       = $config;
        $this->configSource = $configSource;
        $this->transition   = $transition;
    }

    /**
     * Get the action model.
     *
     * @return ActionModel
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the config source.
     *
     * @return null|string
     */
    public function getConfigSource()
    {
        return $this->configSource;
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
     * Get transition.
     *
     * @return Transition
     */
    public function getTransition()
    {
        return $this->transition;
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
