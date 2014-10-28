<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Workflow;

use Netzmacht\Contao\Workflow\Entity\Entity;

/**
 * Interface Condition describes condition being used by the workflow.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Workflow
 */
interface Condition
{
    /**
     * Consider if workflow matches to the entity.
     *
     * @param Entity $entity The entity.
     *
     * @return bool
     */
    public function match(Entity $entity);
}
