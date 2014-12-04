<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Backend\Dca;

class State extends Base
{

    public function generateGroupHeader($label, $foo, $column, $row)
    {
        var_dump($this->defaultDomain);

        $header = array(
            'entityId'       => $this->translate('entityId'),
            'workflowName'   => $this->translate('workflowName'),
            'transitionName' => $this->translate('transitionName'),
            'stepName'       => $this->translate('stepName'),
            'success'        => $this->translate('success'),
            'reachedAt'      => $this->translate('reachedAt'),
        );

        return $label . $this->generateRow($header);
    }

    public function generateRow($row)
    {
        $template = new \BackendTemplate('be_workflow_state_row');
        $template->setData($row);
        $template->formatDate = function($date, $format = 'date') {
            return \Date::parse($GLOBALS['TL_CONFIG'][$format . 'Format'], $date);
        };

        return $template->parse();
    }
}
