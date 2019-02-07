Example workflow implementation
===============================

This example demonstrates how custom workflow types and actions could be implemented. 


## Features

It provides a new DCA called `tl_example` which has a published state.


### Example workflow type

 * Create a workflow type [`ExampleType`]: A custom workflow type has to implement interface [`WorkflowType`].
 * Register it as services tagged with tag `netzmacht.contao_workflow.type`. See [services.yml]
 * Add `example` as new dca palette at [tl_workflow.php]

### Implement a custom action without required user input

 * Create a new Action [`PublishAction`]: Action sets entity property `published`
   to a defined state.
 * Create a new action type factory [`PublishActionFactory`] which is responsible to create the action based on a given configuration.
 * Register action type factory as service tagged with `netzmacht.contao_workflow.action`. See [services.yml]
 * Add `example_publish` as new dca palette at [tl_workflow_action.php]
 * Add new `publish_state` checkbox to action dca to define the new publish state. See [tl_workflow_action.php]
 * Register the new form builder as tagges service with tag ``. See [services.yml]


### Implement a custom action with required user input

 * Follow all steps described at "Implement a custom action without required user input"
 * Create a new [`SendEmailNotificationFormBuilder`] implementing interface [`ActionFormBuilder`]
 

## Setup

 * Add the [`NetzmachtContaoWorkflowExampleBundle`] to your `ContaoManagerPlugin` in you app and rebuild the cache:
 ```php
     BundleConfig::create(NetzmachtContaoWorkflowExampleBundle::class)
         ->setLoadAfter([NetzmachtContaoWorkflowBundle::class])
 ```
 * Run the install tool and update your database
 * Create a new workflow of type `Example`
 * Create the actions, steps and transitions
 * Configure the process


[`WorkflowType`]: ../src/Workflow/Type/WorkflowType.php
[`ActionFormBuilder`]: ../src/Form/Builder/ActionFormBuilder.php
[`ExampleType`]: src/Workflow/Type/ExampleType.php
[`PublishAction`]: src/Workflow/Action/PublishAction.php
[`PublishActionFactory`]: src/Workflow/Action/PublishActionFactory.php
[`SendEmailNotificationFormBuilder`]: src/Workflow/Form/SendEmailNotificationFormBuilder.php
[`NetzmachtContaoWorkflowExampleBundle`]: src/NetzmachtContaoWorkflowExampleBundle.php
[tl_workflow.php]: src/Resources/contao/dca/tl_workflow.php
[tl_workflow_action.php]: src/Resources/contao/dca/tl_workflow_action.php
[services.yml]: src/Resources/config/services.yml
