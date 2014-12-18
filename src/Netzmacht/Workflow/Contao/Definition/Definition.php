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

namespace Netzmacht\Workflow\Contao\Definition;

/**
 * Class Definition stores definition constants.
 *
 * @package Netzmacht\Workflow\Contao\Definition
 */
class Definition
{
    const SOURCE = '__source__';

    const SOURCE_DATABASE = 'database';

    const SOURCE_DCA = 'dca';
}
