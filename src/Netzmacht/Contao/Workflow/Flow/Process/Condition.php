<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Process;


use Netzmacht\Contao\Workflow\Entity\Entity;

interface Condition
{
    /**
     * @param Entity $entity
     * @return bool
     */
    public function match(Entity $entity);

} 