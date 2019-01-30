<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity;

use Netzmacht\Workflow\Data\EntityRepository;

/**
 * Interface RepositoryFactory
 *
 * @package Netzmacht\ContaoWorkflowBundle\Entity
 */
interface RepositoryFactory
{
    /**
     * Check if entity is supported by the repository.
     *
     * @param string $providerName Provider name.
     *
     * @return bool
     */
    public function supports(string $providerName): bool;

    /**
     * Create the repository.
     *
     * @param string $providerName Provider name.
     *
     * @return EntityRepository
     */
    public function create(string $providerName): EntityRepository;
}
