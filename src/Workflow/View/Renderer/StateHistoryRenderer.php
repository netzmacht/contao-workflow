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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\StringUtil;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\Entity;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Manager\Manager;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class StateHistoryRenderer
 *
 * @package Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer
 */
class StateHistoryRenderer extends AbstractStepRenderer
{
    /**
     * The section name.
     *
     * @var string
     */
    protected static $section = 'history';

    /**
     * The workflow manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Config adapter.
     *
     * @var Adapter|Config
     */
    private $configAdapter;

    /**
     * Entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param Manager        $manager
     * @param Translator     $translator    Translator.
     * @param Config|Adapter $configAdapter Config adapter.
     * @param EntityManager  $entityManager Entity manager.
     * @param array          $templates     Mapping between the content type and the default template.
     *
     * @throws \Assert\AssertionFailedException If No section name is defined.
     */
    public function __construct(
        Manager $manager,
        Translator $translator,
        $configAdapter,
        EntityManager $entityManager,
        array $templates = []
    ) {
        parent::__construct($translator, $templates);

        $this->manager       = $manager;
        $this->configAdapter = $configAdapter;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderParameters(View $view): array
    {
        $workflow     = $view->getWorkflow();
        $history      = $view->getItem()->getStateHistory();
        $data         = [];
        $stateColumns = StringUtil::deserialize($workflow->getConfigValue('stepHistoryColumns'), true)
            ?: ['workflow', 'transition', 'step', 'reachedAt', 'user', 'scope'];

        foreach ($history as $state) {
            $row = [];

            foreach ($stateColumns as $column) {
                $row[$column] = $this->renderStateColumn($state, $column);
            }

            $data[$state->getStateId()] = $row;
        }

        $data = array_reverse($data);

        return ['columns' => $stateColumns, 'history' => $data];
    }

    /**
     * Render a state column.
     *
     * @param State  $state  Workflow item state.
     * @param string $column State column.
     *
     * @return string|array|null
     */
    private function renderStateColumn(State $state, string $column)
    {
        switch ($column) {
            case 'workflow':
                return $this->renderWorkflowName($state);

            case 'transition':
                return $this->renderTransitionName($state);

            case 'step':
                return $this->renderStepName($state);

            case 'successful':
                $yesNo = $state->isSuccessful() ? 'yes' : 'no';

                return $this->trans('MSC.' . $yesNo, [], 'contao_default');

            case 'reachedAt':
                return $state->getReachedAt()->format($this->configAdapter->get('datimFormat'));

            case 'user':
            case 'scope':
                return $this->renderMetaData($state, $column);

            default:
                return null;
        }
    }

    /**
     * Render the workflow name.
     *
     * @param State $state Workflow item state.
     *
     * @return string
     */
    private function renderWorkflowName(State $state): string
    {
        try {
            return $this->manager->getWorkflowByName($state->getWorkflowName())->getLabel();
        } catch (WorkflowException $e) {
            return $state->getWorkflowName() ?: '-';
        }
    }

    /**
     * Render the transition name.
     *
     * @param State $state Workflow item state.
     *
     * @return string
     */
    private function renderTransitionName(State $state): string
    {
        try {
            return $this->manager
                ->getWorkflowByName($state->getWorkflowName())
                ->getTransition($state->getTransitionName())
                ->getLabel();
        } catch (WorkflowException $e) {
            return $state->getTransitionName() ?: '-';
        }
    }

    /**
     * Render the step name.
     *
     * @param State $state Workflow item state.
     *
     * @return string
     */
    private function renderStepName(State $state): string
    {
        try {
            return $this->manager
                ->getWorkflowByName($state->getWorkflowName())
                ->getStep($state->getStepName())
                ->getLabel();
        } catch (WorkflowException $e) {
            return $state->getStepName() ?: '-';
        }
    }

    /**
     * Render the meta data.
     *
     * @param State  $state  Workflow item state.
     * @param string $column The meta data column name.
     *
     * @return array|string|null
     */
    private function renderMetaData(State $state, string $column)
    {
        $data = $state->getData();

        if (!isset($data['metadata']) || !is_array($data['metadata'])) {
            return '-';
        }

        switch ($column) {
            case 'user':
                return $this->renderUserName($data['metadata']);

            case 'scope':
                return $this->renderScope($data['metadata']);

            default:
                return '-';
        }
    }

    /**
     * Render the username.
     *
     * @param array $metadata State metadata.
     *
     * @return array|null
     */
    private function renderUserName(array $metadata)
    {
        if (!isset($metadata['userId'])) {
            return null;
        }

        $userId     = EntityId::fromString($metadata['userId']);
        $repository = $this->entityManager->getRepository($userId->getProviderName());
        $user       = $repository->find($userId->getIdentifier());

        if ($user instanceof Entity) {
            $userName = '';

            if ($user->getProviderName() === 'tl_user') {
                $userName = $user->getProperty('name');
            } elseif ($user->getProviderName() === 'tl_member') {
                $userName = $user->getProperty('firstname') . ' ' . $user->getProperty('lastname');
            }

            $userName = [
                'name'    => $userName,
                'username' => $user->getProperty('username') ?: $user->getId()
            ];

            return $userName;
        }

        return $metadata['userId'];
    }

    /**
     * Render the scope.
     *
     * @param array $metadata State metadata.
     *
     * @return string
     */
    private function renderScope(array $metadata): string
    {
        if (!isset($metadata['scope'])) {
            return '-';
        }

        return $this->trans('workflow.history.scope.' . $metadata['scope']);
    }
}
