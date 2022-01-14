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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function in_array;

/** @SuppressWarnings(PHPMD.LongVariable) */
final class NotificationActionFactory implements ActionTypeFactory
{
    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * @param RepositoryManager     $repositoryManager     Repository manager.
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param Translator            $translator            Translator.
     * @param EventDispatcher       $eventDispatcher       The event dispatcher.
     */
    public function __construct(
        RepositoryManager $repositoryManager,
        PropertyAccessManager $propertyAccessManager,
        Translator $translator,
        EventDispatcher $eventDispatcher
    ) {
        $this->repositoryManager     = $repositoryManager;
        $this->propertyAccessManager = $propertyAccessManager;
        $this->translator            = $translator;
        $this->eventDispatcher       = $eventDispatcher;
    }

    public function getCategory(): string
    {
        return 'default';
    }

    public function getName(): string
    {
        return 'notification';
    }

    public function isPostAction(): bool
    {
        return true;
    }

    public function supports(Workflow $workflow): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config, Transition $transition): Action
    {
        return new NotificationAction(
            $this->propertyAccessManager,
            $this->repositoryManager,
            $this->translator,
            $this->eventDispatcher,
            'action_' . $config['id'],
            $config['label'],
            (int) $config['notification_id'],
            $this->determineSuccessStates($config['notification_states'])
        );
    }

    /**
     * Determine the success states.
     *
     * @param mixed $states Raw success state value.
     */
    private function determineSuccessStates($states): int
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
