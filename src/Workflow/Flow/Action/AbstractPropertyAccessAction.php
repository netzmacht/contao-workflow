<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Base;

/** @SuppressWarnings(PHPMD.LongVariable)  */
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
     * @param array<string,mixed>   $config                Configuration values.
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
