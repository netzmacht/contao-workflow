<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\ContaoWorkflowBundle\Exception\RuntimeException;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;

use function strlen;
use function strpos;
use function substr;

final class WorkflowPermissions
{
    private const ACCESS_STEP_PREFIX        = 'netzmacht_workflow.step.';
    private const TRANSIT_TRANSITION_PREFIX = 'netzmacht_workflow.transition.';

    public static function transitTransition(Transition $transition): string
    {
        return self::TRANSIT_TRANSITION_PREFIX . $transition->getName();
    }

    public static function accessStep(Step $step): string
    {
        return self::ACCESS_STEP_PREFIX . $step->getName();
    }

    public static function isTransitTransition(string $attribute): bool
    {
        return strpos($attribute, self::TRANSIT_TRANSITION_PREFIX) === 0;
    }

    public static function isAccessStep(string $attribute): bool
    {
        return strpos($attribute, self::ACCESS_STEP_PREFIX) === 0;
    }

    public static function extractStepName(string $attribute): string
    {
        if (! self::isAccessStep($attribute)) {
            throw new RuntimeException('Invalid step permission: ' . $attribute);
        }

        return substr($attribute, strlen(self::ACCESS_STEP_PREFIX));
    }

    public static function extractTransitionName(string $attribute): string
    {
        if (! self::isTransitTransition($attribute)) {
            throw new RuntimeException('Invalid transition permission: ' . $attribute);
        }

        return substr($attribute, strlen(self::TRANSIT_TRANSITION_PREFIX));
    }
}
