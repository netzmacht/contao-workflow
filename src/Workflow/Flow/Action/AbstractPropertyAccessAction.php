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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Base;

/**
 * Class AbstractPropertyAccessAction
 *
 * @package Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action
 */
abstract class AbstractPropertyAccessAction extends Base implements Action
{
    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    protected $propertyAccessManager;

    /**
     * Construct.
     *
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param string                $name                  Name of the element.
     * @param string                $label                 Label of the element.
     * @param array                 $config                Configuration values.
     */
    public function __construct(
        PropertyAccessManager $propertyAccessManager,
        string $name,
        string $label = '',
        array $config = []
    ) {
        parent::__construct($name, $label, $config);

        $this->propertyAccessManager = $propertyAccessManager;
    }
}
