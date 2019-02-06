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
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class AbstractExampleActionFactory
 */
abstract class AbstractExampleActionFactory implements ActionTypeFactory
{
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
    public function supports(Workflow $workflow): bool
    {
        return $workflow->getProviderName() === 'tl_example';
    }
}
