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

namespace Netzmacht\Contao\Workflow\Backend\Controller;

use Netzmacht\Contao\Workflow\Type\WorkflowTypeRegistry;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Manager\WorkflowManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as TemplateEngine;

/**
 * Class AbstractController
 *
 * @package Netzmacht\Contao\Workflow\Backend\Controller
 */
class AbstractController
{
    /**
     * Workflow manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * Template engine.
     *
     * @var TemplateEngine
     */
    private $renderer;

    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * The workflow type registry.
     *
     * @var WorkflowTypeRegistry
     */
    private $typeRegistry;
}
