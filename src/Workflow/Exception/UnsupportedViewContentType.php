<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Exception;

use InvalidArgumentException;
use Netzmacht\Workflow\Exception\WorkflowException;

final class UnsupportedViewContentType extends InvalidArgumentException implements WorkflowException
{
}
