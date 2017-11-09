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

namespace Netzmacht\Contao\Workflow\Backend\Dca;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class State provides helpers for tl_workflow_state.
 *
 * @package Netzmacht\Contao\Workflow\Backend\Dca
 */
class State extends Base
{
    /**
     * Template engine.
     *
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * Entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var WorkflowManager
     */
    private $manager;

    /**
     * @param TranslatorInterface $translator
     * @param TemplateEngine      $templateEngine
     * @param EntityManager       $entityManager
     * @param WorkflowManager     $manager
     */
    public function __construct(
        TranslatorInterface $translator,
        TemplateEngine $templateEngine,
        EntityManager $entityManager,
        WorkflowManager $manager
    ) {
        parent::__construct($translator);

        $this->templateEngine = $templateEngine;
        $this->entityManager  = $entityManager;
        $this->manager        = $manager;
    }

    /**
     * Apply a filter when looking at the history.
     *
     * @return void
     */
    public function applyFilter(): void
    {
        if (\Input::get('providerName') && \Input::get('id')) {
            $entityId = EntityId::fromProviderNameAndId(
                \Input::get('providerName'),
                \Input::get('id')
            );

            $session = \Session::getInstance();
            $filter  = $session->get('filter');

            $filter['tl_workflow_state'] = ['entityId' => (string) $entityId];
            $session->set('filter', $filter);

            \Backend::redirect(\Backend::addToUrl('providerName=', true, ['providerName', 'id']));
        }
    }

    /**
     * Generate group header.
     *
     * @param string $label Current label.
     *
     * @return string
     */
    public function generateGroupHeader(string $label): string
    {
        $header = [
            'entityId'       => $this->translate('entityId.0'),
            'workflowName'   => $this->translate('workflowName.0'),
            'transitionName' => $this->translate('transitionName.0'),
            'stepName'       => $this->translate('stepName.0'),
            'success'        => $this->translate('success.0'),
            'reachedAt'      => $this->translate('reachedAt.0'),
        ];

        return $label . $this->templateEngine->render('toolkit:be:be_workflow_state_row.html5', $header);
    }

    /**
     * Generate the row.
     *
     * @param array $row Row.
     *
     * @return string
     */
    public function generateRow(array $row): string
    {
        try {
            $entityId = EntityId::fromString($row['entityId']);
            $entity   = $this->entityManager
                ->getRepository($entityId->getProviderName())
                ->find($entityId->getIdentifier());

            $workflow = $this->manager->getWorkflow($entityId, $entity);

            if ($workflow) {
                $row['workflowName']   = $workflow->getLabel();
                $row['transitionName'] = $workflow->getTransition($row['transitionName'])->getLabel();
                $row['stepName']       = $workflow->getStep($row['stepName'])->getLabel();
            }
        } catch (\Exception $e) {
            // Catch exception here so if the definition has changes no error is thrown.
        }

        $row['success'] = $this->translate($row['success'] ? 'yes' : 'no', [], 'MSC');

        if (is_numeric($row['reachedAt'])) {
            $row['reachedAt'] = \Date::parse(\Config::get('datimFormat'), $row['reachedAt']);
        }

        return $this->templateEngine->render('toolkit:be:be_workflow_state_row.html5', $row);
    }
}
