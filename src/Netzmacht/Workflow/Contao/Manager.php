<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Workflow\Contao\Factory\EntityFactory;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Factory;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Security\User;

class Manager
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @param User          $user
     * @param Factory       $factory
     * @param EntityFactory $entityFactory
     */
    function __construct(User $user, Factory $factory, EntityFactory $entityFactory)
    {
        $this->user    = $user;
        $this->factory = $factory;
        $this->entityFactory = $entityFactory;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $model
     * @param $providerName
     *
     * @return Entity
     */
    public function createEntity($model, $providerName = null)
    {
        return $this->entityFactory->createEntity($model, $providerName);
    }

    /**
     * @param Entity      $model
     * @param string|null $transitionName
     * @param string|null $workflowType
     *
     * @return bool|TransitionHandler
     */
    public function handle(Entity $model, $transitionName = null, $workflowType = null)
    {
        // If no manager could be created that means there is no workflow registered.
        // Catch exception and just return false
        try {
            $manager = $this->factory->createManager($model->getProviderName(), $workflowType);
        } catch(\RuntimeException $e) {
            return false;
        }

        $entityId = EntityId::fromProviderNameAndId($model->getProviderName(), $model->getId());
        $item     = $manager->createItem($entityId, $model);

        return $manager->handle($item, $transitionName);
    }

    /**
     * @param $type
     *
     * @return \Netzmacht\Workflow\Form\Form
     */
    public function createForm($type)
    {
        return $this->factory->createForm($type);
    }
}
