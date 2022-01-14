<?php

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
 * @SuppressWarnings(PHPMD.LongVariable)
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
     */
    public function __invoke(BuildNotificationTokensEvent $event): void
    {
        $entity = $event->getItem()->getEntity();
        if (! $this->propertyAccessManager->supports($entity)) {
            return;
        }

        try {
            $formatter = $this->dcaManager->getFormatter($event->getItem()->getEntityId()->getProviderName());
        } catch (AssertionFailed $exception) {
            return;
        }

        $context  = null;
        $accessor = $this->propertyAccessManager->provideAccess($entity);

        if ($entity instanceof Model) {
            $context = new DC_Table($event->getItem()->getEntityId()->getProviderName());

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
