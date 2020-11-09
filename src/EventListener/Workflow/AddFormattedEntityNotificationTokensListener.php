<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Workflow;

use Contao\DC_Table;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Notification\BuildNotificationTokensEvent;
use Throwable;
use function iterator_to_array;

/**
 * Class AddFormattedEntityNotificationTokensListener enriches the notifications with formatted values.
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 */
final class AddFormattedEntityNotificationTokensListener
{
    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * Data container manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * Constructor.
     *
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param DcaManager            $dcaManager            Data container manager.
     */
    public function __construct(PropertyAccessManager $propertyAccessManager, DcaManager $dcaManager)
    {
        $this->dcaManager            = $dcaManager;
        $this->propertyAccessManager = $propertyAccessManager;
    }

    /**
     * Invoke.
     *
     * @param BuildNotificationTokensEvent $event The subscribed event.
     *
     * @return void
     */
    public function __invoke(BuildNotificationTokensEvent $event): void
    {
        $entity = $event->getItem()->getEntity();
        if (!$this->propertyAccessManager->supports($entity)) {
            return;
        }

        try {
            $formatter = $this->dcaManager->getFormatter($event->getItem()->getEntityId()->getProviderName());
        } catch (AssertionFailed $exception) {
            return;
        }

        $context = null;
        if ($entity instanceof Model) {
            $accessor = $this->propertyAccessManager->provideAccess($entity);
            $context  = new DC_Table($event->getItem()->getEntityId()->getProviderName());

            $context->activeRecord = (object) iterator_to_array($accessor->getIterator());
        }

        foreach ($accessor as $key => $value) {
            try {
                $event->addToken('formatted_' . $key, $formatter->formatValue($key, $value, $context));
            } catch (Throwable $exception) {
                // Skip if any error occurs
            }
        }
    }
}
