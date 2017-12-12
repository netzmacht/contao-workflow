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

namespace Netzmacht\ContaoWorkflowBundle\Entity;

use Netzmacht\ContaoWorkflowBundle\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityRepository;

/**
 * Class DelegatingRepositoryFactory
 *
 * @package Netzmacht\ContaoWorkflowBundle\Entity
 */
class DelegatingRepositoryFactory implements RepositoryFactory
{
    /**
     * Entity repository factories.
     *
     * @var RepositoryFactory[]
     */
    private $factories;

    /**
     * DelegatingRepositoryFactory constructor.
     *
     * @param RepositoryFactory[] $factories Entity repository factories.
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritDoc}
     */
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
