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

namespace Netzmacht\ContaoWorkflowExampleBundle\Workflow\Type;

use Netzmacht\ContaoWorkflowBundle\Workflow\Type\AbstractWorkflowType;

/**
 * Class ExampleType
 */
final class ExampleType extends AbstractWorkflowType
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct('example', ['tl_example']);
    }
}
