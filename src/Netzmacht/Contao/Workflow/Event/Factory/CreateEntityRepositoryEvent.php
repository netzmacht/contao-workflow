<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Event\Factory;

use Netzmacht\Contao\Workflow\Entity\EntityRepository;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateRepositoryEvent is raised than an repository should be created.
 *
 * @package Netzmacht\Contao\Workflow\Factory\Event
 */
class CreateEntityRepositoryEvent extends Event
{
    const NAME = 'workflow.factory.createRepository-entity-repository';

    /**
     * Name of the provider (table).
     *
     * @var string
     */
    private $providerName;

    /**
     * Repository of the entity.
     *
     * @var EntityRepository
     */
    private $repository;

    /**
     * Construct.
     *
     * @param string $providerName Name of the provider.
     */
    public function __construct($providerName)
    {
        $this->providerName = $providerName;
    }

    /**
     * Get the name of the provider.
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Get the repository.
     *
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Set the repository.
     *
     * @param EntityRepository $repository The entity repository.
     *
     * @return $this
     */
    public function setRepository(EntityRepository $repository)
    {
        $this->repository = $repository;

        return $this;
    }
}
