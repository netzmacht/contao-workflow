<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Entity;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use Netzmacht\Contao\Workflow\Flow\State;

interface Entity extends ModelInterface
{
    /**
     * @return State
     */
    public function getState();

    /**
     * @param State $state
     * @return mixed
     */
    public function transit(State $state);

} 