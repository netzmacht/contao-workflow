<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity;

use Netzmacht\Workflow\Data\EntityRepository;

interface RepositoryFactory
{
    /**
     * Check if entity is supported by the repository.
     *
     * @param string $providerName Provider name.
     */
    public function supports(string $providerName): bool;

    /**
     * Create the repository.
     *
     * @param string $providerName Provider name.
     */
    public function create(string $providerName): EntityRepository;
}
