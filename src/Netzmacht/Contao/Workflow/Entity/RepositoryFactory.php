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

/**
 * Interface RepositoryFactory describes the contract for a repository factory.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
interface RepositoryFactory
{
    /**
     * Create a repository for a given provider name.
     *
     * @param string $providerName Provider name.
     *
     * @return EntityRepository
     */
    public function createRepository($providerName);
}
