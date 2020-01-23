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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel;

use Netzmacht\Contao\Toolkit\Data\Model\Specification as ModelSpecification;
use Netzmacht\Workflow\Data\Specification;

/**
 * Interface ContaoModelSpecification
 */
interface ContaoModelSpecificationAwareSpecification extends Specification
{
    /**
     * Get specification as model specification.
     *
     * @return ModelSpecification
     */
    public function asModelSpecification(): ModelSpecification;
}
