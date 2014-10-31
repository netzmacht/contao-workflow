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


use ContaoCommunityAlliance\DcGeneral\Data\DefaultDataProvider;
use Netzmacht\Contao\Workflow\Contao\DataContainer;
use Netzmacht\Contao\Workflow\Factory\Event\CreateEntityRepositoryEvent;
use Netzmacht\Contao\Workflow\Model\ContaoStateRepository;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntityManager implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateEntityRepositoryEvent::NAME => 'handleCreateEntityRepository',
        );
    }

    /**
     * Handle the createRepository entity repository event.
     *
     * @param CreateEntityRepositoryEvent $event Event subscribed to.
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
                // TODO: Handle DcGeneral

            default:
                return;
        }

        $event->setRepository(new EntityRepository($provider));
    }

    /**
     * Create an entity repository.
     *
     * @param string $providerName The provider name.
     *
     * @throws \InvalidArgumentException If repository could not be created.
     *
     * @return EntityRepository
     */
    public function getRepository($providerName)
    {
        $event = new CreateEntityRepositoryEvent($providerName);
        $this->getEventDispatcher()->dispatch($event::NAME, $event);

        if (!$event->getRepository()) {
            throw new \InvalidArgumentException(
                sprintf('Could not createRepository entity repository "%s".', $providerName)
            );
        }

        return $event->getRepository();
    }

    /**
     * Create the state repository.
     *
     * @return StateRepository
     */
    public function getStateRepository()
    {
        return new ContaoStateRepository(\Database::getInstance());
    }

    /**
     * Get the driver type.
     *
     * @param string $providerName Provider name.
     *
     * @throws \InvalidArgumentException If dataContainer could not be loaded.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getDriver($providerName)
    {
        DataContainer::load($providerName);

        if (!isset($GLOBALS['TL_DCA'][$providerName]['config']['dataContainer'])) {
            throw new \InvalidArgumentException(sprintf('Could not detect data container type of "%s"', $providerName));
        }

        return $GLOBALS['TL_DCA'][$providerName]['config']['dataContainer'];
    }

    /**
     * Get the event dispatcher.
     *
     * @return EventDispatcher
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getEventDispatcher()
    {
        return $GLOBALS['container']['event-dispatcher'];
    }
}
