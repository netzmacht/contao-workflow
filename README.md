
Workflow integration for Contao CMS
===================================


[![Build Status](http://img.shields.io/travis/netzmacht/contao-workflow/master.svg?style=flat-square)](https://travis-ci.org/netzmacht/contao-workflow)
[![Version](http://img.shields.io/packagist/v/netzmacht/contao-workflow.svg?style=flat-square)](http://packagist.com/packages/netzmacht/contao-workflow)
[![Code quality](http://img.shields.io/scrutinizer/g/netzmacht/contao-workflow.svg?style=flat-square)](https://scrutinizer-ci.com/g/netzmacht/contao-workflow/)
[![Code coverage](http://img.shields.io/scrutinizer/coverage/g/netzmacht/contao-workflow.svg?style=flat-square)](https://scrutinizer-ci.com/g/netzmacht/contao-workflow/)

This library is an integration of the [workflow library](http://github.com/netzmacht/workflow) for the Contao CMS. It 
provides a backend user interface to define workflows.

This extension is **not a standalone** workflow library. In other words: You can use this library to build you custom 
workflow type on top but it won't automatically integrates the workflow in the backend datacontainer drivers neighter
in the frontend.

Definitions
-----------

A workflow is defined by following concepts:

**Workflow**
 * There can be multiple workflows.
 * Workflows are defined for an entity type (typically an database table). 
 * Workflows have a type.
 
**Step**
 * A workflow has a number of steps which an entity reach during it's lifecycle
 * A step allows a number of transitions which leads to the next step.
 * A step can be final. No more transitions are allowed after that.

**Transition**
 * Moving from step *A* to step *B* is called transition.
 * A workflow has exactly one start transition.
 * A transition has one target step.
 * A transition executes actions during moving to the next step.
 * A transition depends on *preconditions* and *conditions* which has to be fullfilled.
 * A transition can be available (potentially executable) or allowed (can be executed). 
 
**Action**
 * An action contains to an transition
 * It executes all logic. Entity manipulation, notifications
 * An action can require user input. Input data are provided by a form.

**Transition conditions**
 * There a two types of transition conditions. Pre conditions and conditions.
 * Preconditions have to be fullfilled so that a transition is available for a user.
 * Conditions are checked after user input. So it's purpose is for validation.
 
**Entity**
 * An entity is a data being in a workflow lifecycle. 
 * [netzmacht/workflow](http://github.com/netzmacht/workflow) is totally independent of the data type.
 * nezmacht/contao-workflow uses the [ModelInterface][netzmacht/workflow](https://github.com/contao-community-alliance/dc-general/blob/develop/src/ContaoCommunityAlliance/DcGeneral/Data/ModelInterface.php)
  of the DC General as convention.
 
**State**
 * Each try of a transition is stored as a state.
 * There can be states of successful transitions or failed transitions.
 * Each state can contain workflow data.
 
**Item**
 * An item is a wrapper for the entity to provide workflow meta data.
 * It knows corresponding workflow and the state history.

How to use it
-------------

```php
<?php 

// Get the workflow manager from the dependency container

/** @var Netzmacht\Workflow\Contao\Manager; */
$manager = $container['workflow.manager'];

// Load model and create the entity.
$model   = \ContentModel::findByPK(10);
$entity  = $manager->createEntity($model);

// Create the transition handler to perform transition start on the entity. Optional select your workflow type
// which your implementation supports.
$handler = $manager->handle($entity, 'start', 'optional-workflow-type');

// if there is no supported workflow for the entity, handler will be false
if ($handler) {
    // The transition handler provides access to the item. The item contains the entity.
    $item = $handler->getItem();
    
    // create a backend form. Support for frontend forms are planned.
    $form = $manager->createForm(TL_MODE);
    
    // validate user input and transition conditions
    if ($handler->validate($form)) {
        // The transit method returns the new state. States are automatically stored in the state repository.
        // Entity changes are stored as well.
        $state = $handler->transit();
        
        if (!$state->isSuccessful()) {
            // Something went wrong.
            
            $errors = $handler->getErrorCollection();
        }
    } elseif ($handler->isUnserInputRequired()) {
        // form was not submitted or has errors. recreate it
        // pass form to the view
        $rendered = $form->render();
    } else {
        // Something went wrong.
        
        $errors = $handler->getErrorCollection();
    }
}

```
