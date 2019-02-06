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

namespace Netzmacht\ContaoWorkflowExampleBundle\Workflow\Action;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Swift_Mailer;

final class SendEmailNotificationFactory implements ActionTypeFactory
{
    /**
     * Mailer service.
     *
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * SendEmailNotificationFactory constructor.
     *
     * @param Swift_Mailer $mailer Mailer service.
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritDoc}
     */
    public function getCategory(): string
    {
        return 'example';
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'example_notification';
    }

    /**
     * {@inheritDoc}
     */
    public function isPostAction(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Workflow $workflow): bool
    {
        return $workflow->getProviderName() === 'tl_example';
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config, Transition $transition): Action
    {
        return new SendEmailNotificationAction(
            $this->mailer,
            'action_' . $config['id'],
            $config['label'],
            $config['notification_recipient'],
            $config['notification_message']
        );
    }
}
