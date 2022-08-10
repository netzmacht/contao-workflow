<?php

declare(strict_types=1);

namespace spec\Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;

final class WorkflowPermissionsSpec extends ObjectBehavior
{
    public function it_creates_transit_transition(Transition $transition): void
    {
        $transition->getName()->willReturn('spec');
        $this->transitTransition($transition)->shouldReturn('netzmacht_workflow.transition.spec');
    }

    public function it_extracts_transition_name(): void
    {
        $this->extractTransitionName('netzmacht_workflow.transition.spec')->shouldReturn('spec');
    }

    public function it_creates_access_step(Step $step): void
    {
        $step->getName()->willReturn('spec');
        $this->accessStep($step)->shouldReturn('netzmacht_workflow.step.spec');
    }

    public function it_extracts_step_name(): void
    {
        $this->extractStepName('netzmacht_workflow.step.spec')->shouldReturn('spec');
    }
}
