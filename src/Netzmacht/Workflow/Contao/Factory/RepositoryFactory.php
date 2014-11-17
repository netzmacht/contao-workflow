<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Factory;


use ContaoCommunityAlliance\DcGeneral\Data\DefaultDataProvider;
use Netzmacht\Workflow\Contao\Data\EntityRepository;
use Netzmacht\Workflow\Contao\Dca\Helper\DcaLoader;
use Netzmacht\Workflow\Contao\Factory\Event\CreateEntityRepositoryEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RepositoryFactory implements EventSubscriberInterface
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @return array|void
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateEntityRepositoryEvent::NAME => 'handleCreateEntityRepository'
        );
    }

    /**
     * @param CreateEntityRepositoryEvent $event
     */
    public function handleCreateEntityRepository(Event\CreateEntityRepositoryEvent $event)
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

        $repository = new EntityRepository($provider);
        $event->setRepository($repository);
    }

    /**
     * @param $providerName
     *
     * @return EntityRepository
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
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getDriver($providerName)
    {
        DcaLoader::load($providerName);

        if (!isset($GLOBALS['TL_DCA'][$providerName]['config']['dataContainer'])) {
            return null;
        }

        return $GLOBALS['TL_DCA'][$providerName]['config']['dataContainer'];
    }
}
