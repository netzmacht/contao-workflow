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

\TemplateLoader::addFiles(array(
        'be_workflow_transition_form' => 'system/modules/workflow/templates',
        'be_workflow_state_row'       => 'system/modules/workflow/templates',
        'workflow_errors'             => 'system/modules/workflow/templates',
    )
);
