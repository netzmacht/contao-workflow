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


interface RepositoryFactory
{
    /**
     * @param $providerName
     * @return EntityRepository
     */
    public function createRepository($providerName);
}
