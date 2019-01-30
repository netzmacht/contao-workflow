<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Base;

/**
 * Class AbstractAction which uses an form builder to create user input form data.
 */
abstract class AbstractAction extends Base implements Action
{
    use GetEntity;
}
