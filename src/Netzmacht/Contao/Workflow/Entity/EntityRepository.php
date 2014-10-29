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

use ContaoCommunityAlliance\DcGeneral\Data\DataProviderInterface as DataProvider;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;

/**
 * Class EntityRepository stores an entity.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
class EntityRepository
{
    /**
     * The used data provider.
     *
     * @var DataProvider
     */
    private $provider;

    /**
     * Construct.
     *
     * @param DataProvider $provider The data provider.
     */
    public function __construct(DataProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Add an entity to the repository.
     *
     * @param Entity $entity The new entity.
     *
     * @return void
     */
    public function add(Entity $entity)
    {
        $this->provider->save($entity);
    }
}
