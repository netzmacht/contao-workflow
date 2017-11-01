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

namespace Netzmacht\Contao\Workflow\Backend\Dca;

use Netzmacht\Contao\Workflow\Model\WorkflowModel;
use Netzmacht\Contao\Workflow\ServiceContainerTrait;

/**
 * Class Step used for tl_workflow_step callbacks.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class Step
{
    use ServiceContainerTrait;

    /**
     * Adjust the input mask.
     *
     * @param \DataContainer $dataContainer Data container.
     *
     * @return void
     */
    public function adjustEditMask($dataContainer)
    {
        $workflow     = WorkflowModel::findByPk(CURRENT_ID);
        $typeProvider = $this->getServiceProvider()->getTypeProvider();

        if (!$workflow || !$typeProvider->hasType($workflow->type)) {
            return;
        }

        $workflowType = $typeProvider->getType($workflow->type);
        if ($workflowType->hasFixedSteps()) {
            $GLOBALS['TL_DCA']['tl_workflow_step']['fields']['name']['inputType']                  = 'select';
            $GLOBALS['TL_DCA']['tl_workflow_step']['fields']['name']['options']                    = $workflowType->getStepNames();
            $GLOBALS['TL_DCA']['tl_workflow_step']['fields']['name']['eval']['includeBlankOption'] = true;
        } else {
            $GLOBALS['TL_DCA']['tl_workflow_step']['fields']['name']['save_callback'][] = array(
                'Netzmacht\Contao\Workflow\Backend\Common',
                'createName'
            );
        }
    }
}
