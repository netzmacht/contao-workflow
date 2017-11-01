<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

return array(
    'Netzmacht\Contao\Workflow\Definition\Database\ManagerBuilder',
    'Netzmacht\Contao\Workflow\Definition\Database\WorkflowBuilder',
    'Netzmacht\Contao\Workflow\Definition\Database\PermissionSubscriber',
    'Netzmacht\Contao\Workflow\Definition\Database\ConditionBuilder',
    'Netzmacht\Contao\Workflow\Form\FormFactory',
);
