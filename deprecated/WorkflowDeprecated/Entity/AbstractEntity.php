<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Entity;

use ContaoCommunityAlliance\DcGeneral\Data\AbstractModel;
use Netzmacht\Contao\Workflow\Entity;

abstract class AbstractEntity extends AbstractModel implements Entity
{
    use WorkflowStateTrait;
} 
