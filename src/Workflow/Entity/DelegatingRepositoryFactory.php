<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity;

use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityRepository;

final class DelegatingRepositoryFactory implements RepositoryFactory
{
    /**
     * Entity repository factories.
     *
     * @var RepositoryFactory[]
     */
    private $factories;

    /**
     * @param RepositoryFactory[] $factories Entity repository factories.
     */
    public function __construct(iterable $factories)
    {
        $this->factories = $factories;
    }

    public function supports(string $providerName): bool
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($providerName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When Entity type is not supported.
     */
    public function create(string $providerName): EntityRepository
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($providerName)) {
                return $factory->create($providerName);
            }
        }

        throw UnsupportedEntity::withProviderName($providerName);
    }
}
