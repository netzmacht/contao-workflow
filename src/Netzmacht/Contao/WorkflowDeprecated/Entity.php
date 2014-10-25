<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use Netzmacht\Contao\WorkflowDeprecated\Flow\State;

interface Entity extends ModelInterface
{
    /**
     * @return State|null
     */
    public function getWorkflowState();

    /**
     * @param State $state
     * @return $this
     */
    public function setWorkflowState(State $state);

}