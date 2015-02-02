<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Workflow\Contao\Data;

use ContaoCommunityAlliance\DcGeneral\Data\DefaultDataProvider;
use Netzmacht\Contao\Toolkit\Dca;
use Netzmacht\Workflow\Contao\Data\Event\CreateEntityRepositoryEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

/**
 * Class RepositoryFactory creates entity repositories.
 *
 * @package Netzmacht\Workflow\Contao\Data
 */
class RepositoryFactory implements EventSubscriber
{
    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Construct.
     *
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateEntityRepositoryEvent::NAME => 'handleCreateEntityRepository'
        );
    }

    /**
     * Handle the create entity event.
     *
     * @param CreateEntityRepositoryEvent $event The received event.
     *
     * @return void
     */
    public function handleCreateEntityRepository(CreateEntityRepositoryEvent $event)
    {
        $providerName = $event->getProviderName();
        $driver       = $this->getDriver($providerName);

        switch($driver) {
            case 'Table':
                $provider = new DefaultDataProvider();
                $provider->setBaseConfig(array('source' => $providerName));
                break;

            case 'General':
                // @codingStandardsIgnoreStart
                // TODO: Handle DcGeneral
                // @codingStandardsIgnoreEnd

            default:
                return;
        }

        $repository = new EntityRepository($provider);
        $event->setRepository($repository);
    }

    /**
     * Create an entity repository.
     *
     * @param string $providerName The provider name.
     *
     * @return EntityRepository
     *
     * @throws \RuntimeException If no repository was created.
     */
    public function create($providerName)
    {
        $event = new CreateEntityRepositoryEvent($providerName);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        if (!$event->getRepository()) {
            throw new \RuntimeException(sprintf('Could not create entity repository for "%s"', $providerName));
        }

        return $event->getRepository();
    }

    /**
     * Get the driver type.
     *
     * @param string $providerName Provider name.
     *
     * @return string|null
     */
    private function getDriver($providerName)
    {
        $definition = Dca::load($providerName);

        if (isset($definition['config']['dataContainer'])) {
            return $definition['config']['dataContainer'];
        }

        return null;
    }
}
