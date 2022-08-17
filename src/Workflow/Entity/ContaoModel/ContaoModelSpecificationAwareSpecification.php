<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel;

use Netzmacht\Contao\Toolkit\Data\Model\Specification as ModelSpecification;
use Netzmacht\Workflow\Data\Specification;

/**
 * @SuppressWarnings(PHPMD.LongClassName)
 */
interface ContaoModelSpecificationAwareSpecification extends Specification
{
    /**
     * Get specification as model specification.
     */
    public function asModelSpecification(): ModelSpecification;
}
