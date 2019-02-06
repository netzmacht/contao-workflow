Example workflow implementation
===============================

This example demonstrates how custom workflow types and actions could be implemented.
 
 provides a new DCA called `tl_example` which has a published state.

## Example workflow type

 * Create a workflow type [`ExampleType`](src/Workflow/Type/ExampleType.php): A custom workflow type has to implement interface `Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowType`.
 * Register it as services tagged with tag `netzmacht.contao_workflow.type`. See [services.yml](src/Resources/config/services.yml)
 * Add `example` as new dca palette at [tl_workflow](src/Resources/contao/dca/tl_workflow.php)

## Implement a custom action without required user input

 * Create a new Action [PublishAction](src/Workflow/Action/PublishAction.php): Action sets entity property `published`
   to a defined state.
 * Create a new action type factory [PublishActionFactory](src/Workflow/Action/PublishActionFactory.php) which is responsible to create the action based on a given configuration.
 * Register action type factory as service tagged with ``
