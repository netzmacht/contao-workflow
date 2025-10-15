<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\NotificationCenter;

use Override;
use Terminal42\NotificationCenterBundle\NotificationType\NotificationTypeInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\AnythingTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\EmailTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\Factory\TokenDefinitionFactoryInterface;

final class WorkflowTransitionNotificationType implements NotificationTypeInterface
{
    public const string NAME = 'workflow_transition';

    public function __construct(private readonly TokenDefinitionFactoryInterface $factory)
    {
    }

    #[Override]
    public function getName(): string
    {
        return self::NAME;
    }

    /** {@inheritDoc} */
    #[Override]
    public function getTokenDefinitions(): array
    {
        return [
            $this->factory->create(EmailTokenDefinition::class, 'user_email', 'workflow.user_email'),
            $this->factory->create(AnythingTokenDefinition::class, 'entity_*', 'workflow.entity_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'formatted_*', 'workflow.formatted_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'properties_*', 'workflow.properties_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'step_*', 'workflow.step_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'transition_*', 'workflow.transition_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'payload_*', 'workflow.payload_*'),
            $this->factory->create(AnythingTokenDefinition::class, 'successful', 'workflow.successful'),
            $this->factory->create(AnythingTokenDefinition::class, 'date', 'workflow.date'),
            $this->factory->create(AnythingTokenDefinition::class, 'user_*', 'workflow.user_*'),
        ];
    }
}
