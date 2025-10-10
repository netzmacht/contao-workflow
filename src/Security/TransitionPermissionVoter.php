<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Netzmacht\Workflow\Flow\Transition;
use Override;

/**
 * @deprecated Deprecated since version 2.3.0 and will be removed in version 3.0.0.
 *
 * @psalm-suppress DeprecatedClass
 */
final class TransitionPermissionVoter extends AbstractPermissionVoter
{
    #[Override]
    protected function getSubjectClass(): string
    {
        return Transition::class;
    }
}
