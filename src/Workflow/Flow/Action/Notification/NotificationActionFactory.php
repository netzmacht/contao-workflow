<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Notification;

use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use function in_array;
use Symfony\Component\Translation\TranslatorInterface as Translator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class NotificationActionFactory
 */
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
     * NotificationActionFactory constructor.
     *
     * @param RepositoryManager     $repositoryManager     Repository manager.
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param TranslatorInterface   $translator            Translator.
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

    /**
     * {@inheritDoc}
     */
    public function getCategory(): string
    {
        return 'default';
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'notification';
    }

    /**
     * {@inheritDoc}
     */
    public function isPostAction(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
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
     *
     * @return int
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
