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
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Transition;
use NotificationCenter\Model\Notification;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class NotificationAction send an notification as post action.
 */
final class NotificationAction extends AbstractAction
{
    public const STATE_ALL     = 3;
    public const STATE_SUCCESS = 1;
    public const STATE_FAILED  = 2;

    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccess;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Success states.
     *
     * @var int
     */
    private $successStates;

    /**
     * Notification ids.
     *
     * @var int
     */
    private $notificationId;

    /**
     * Construct.
     *
     * @param PropertyAccessManager $propertyAccess    Property access manager.
     * @param RepositoryManager     $repositoryManager Repository manager.
     * @param Translator            $translator        Translator.
     * @param EventDispatcher       $eventDispatcher   The event dispatcher.
     * @param string                $name              Name of the element.
     * @param string                $label             Label of the element.
     * @param int                   $notificationId    Notification id.
     * @param int                   $successStates     Success states.
     * @param array                 $config            Configuration values.
     */
    public function __construct(
        PropertyAccessManager $propertyAccess,
        RepositoryManager $repositoryManager,
        Translator $translator,
        EventDispatcher $eventDispatcher,
        string $name,
        string $label,
        int $notificationId,
        int $successStates = self::STATE_ALL,
        array $config = []
    ) {
        parent::__construct($name, $label, $config);

        $this->repositoryManager = $repositoryManager;
        $this->propertyAccess    = $propertyAccess;
        $this->translator        = $translator;
        $this->notificationId    = $notificationId;
        $this->successStates     = $successStates;
        $this->eventDispatcher   = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function validate(Item $item, Context $context): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $latestState = $item->getLatestState(false);
        if ($latestState === false || !$this->matchSuccessState($latestState)) {
            return;
        }

        $notification = $this->repositoryManager->getRepository(Notification::class)->find($this->notificationId);
        if (!$notification instanceof Notification) {
            return;
        }

        $notification->send($this->buildNotificationTokens($transition, $item, $context));
    }

    /**
     * Check if success state is matched.
     *
     * @param State $latestState Given success state.
     *
     * @return bool
     */
    private function matchSuccessState(State $latestState): bool
    {
        if ($latestState->isSuccessful()) {
            return ($this->successStates & self::STATE_SUCCESS) === self::STATE_SUCCESS;
        }

        return ($this->successStates & self::STATE_FAILED) === self::STATE_FAILED;
    }

    /**
     * Build notification tokens.
     *
     * @param Transition $transition Active transition.
     * @param Item       $item       Workflow item.
     * @param Context    $context    Transition context.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function buildNotificationTokens(Transition $transition, Item $item, Context $context): array
    {
        $workflow        = $transition->getWorkflow();
        $currentStepName = $item->getCurrentStepName();
        $latestState     = $item->getLatestState(false);
        $step            = null;
        $entity          = $item->getEntity();
        $tokens          = [
            'admin_email'     => ($GLOBALS['TL_ADMIN_EMAIL'] ?? ''),
            'transition_name' => $transition->getName(),
            'successful'      => $latestState
                ? $this->translator->trans('workflow.successful.yes')
                : $this->translator->trans('workflow.successful.no'),
        ];

        foreach ($transition->getConfig() as $name => $value) {
            $tokens['transition_' . $name] = StringUtil::deserialize($value);
        }

        if ($workflow->hasStep($currentStepName)) {
            $step                = $workflow->getStep($currentStepName);
            $tokens['step_name'] = $step->getName();

            foreach ($step->getConfig() as $name => $value) {
                $tokens['step_' . $name] = StringUtil::deserialize($value);
            }
        }

        foreach ($context->getProperties()->toArray() as $name => $value) {
            $tokens['property_' . $name] = $value;
        }

        foreach ($context->getPayload()->toArray() as $name => $value) {
            $tokens['payload_' . $name] = $value;
        }

        if ($this->propertyAccess->supports($entity)) {
            $propertyAccessor = $this->propertyAccess->provideAccess($entity);

            foreach ($propertyAccessor as $name => $value) {
                $tokens['entity_' . $name] = StringUtil::deserialize($value);
            }
        }

        $event = new BuildNotificationTokensEvent($transition, $item, $context, $tokens);
        $this->eventDispatcher->dispatch('netzmacht.contao_workflow.build_notification_tokens', $event);

        return $event->getTokens();
    }
}
