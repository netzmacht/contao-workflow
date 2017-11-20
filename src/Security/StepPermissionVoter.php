<?php

/**
 * contao-workflow.
 *
 * @package    contao-workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Workflow\Security;

use Netzmacht\Workflow\Flow\Step;
/**
 * Class WorkflowPermissionVoter
 *
 * @package Netzmacht\Contao\Workflow\Security
 */
class StepPermissionVoter extends AbstractPermissionVoter
{
    /**
     * {@inheritDoc}
     */
    protected function getSubjectClass(): string
    {
        return Step::class;
    }
}
