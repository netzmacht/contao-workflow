<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Notification;

use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Override;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;
use Terminal42\NotificationCenterBundle\NotificationCenter;

use function in_array;

/** @SuppressWarnings(PHPMD.LongVariable) */
final class NotificationActionFactory implements ActionTypeFactory
{
    /**
     * @param RepositoryManager     $repositoryManager     Repository manager.
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param Translator            $translator            Translator.
     * @param EventDispatcher       $eventDispatcher       The event dispatcher.
     */
    public function __construct(
        private readonly PropertyAccessManager $propertyAccessManager,
        private readonly Translator $translator,
        private readonly EventDispatcher $eventDispatcher,
        private readonly NotificationCenter $notificationCenter,
    ) {
    }

    #[Override]
    public function getCategory(): string
    {
        return 'default';
    }

    #[Override]
    public function getName(): string
    {
        return 'notification';
    }

    #[Override]
    public function isPostAction(): bool
    {
        return true;
    }

    #[Override]
    public function supports(Workflow $workflow): bool
    {
        return true;
    }

     /** {@inheritDoc} */
    #[Override]
    public function create(array $config, Transition $transition): Action
    {
        return new NotificationAction(
            $this->propertyAccessManager,
            $this->translator,
            $this->eventDispatcher,
            $this->notificationCenter,
            'action_' . $config['id'],
            $config['label'],
            (int) $config['notification_id'],
            $this->determineSuccessStates($config['notification_states']),
        );
    }

    /**
     * Determine the success states.
     *
     * @param mixed $states Raw success state value.
     */
    private function determineSuccessStates(mixed $states): int
    {
        $value  = 0;
        $states = StringUtil::deserialize($states, true);

        if (in_array('success', $states, true)) {
            $value |= NotificationAction::STATE_SUCCESS;
        }

        if (in_array('failed', $states, true)) {
            $value |= NotificationAction::STATE_FAILED;
        }

        return $value;
    }
}
