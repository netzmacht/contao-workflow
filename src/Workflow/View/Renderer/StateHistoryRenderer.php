<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Assert\AssertionFailedException;
use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Model;
use Contao\StringUtil;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Manager\Manager;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function array_reverse;
use function assert;
use function is_array;

final class StateHistoryRenderer extends AbstractStepRenderer
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
     * @param Translator           $translator    Translator.
     * @param Config|Adapter       $configAdapter Config adapter.
     * @param EntityManager        $entityManager Entity manager.
     * @param array<string,string> $templates     Mapping between the content type and the default template.
     *
     * @throws AssertionFailedException If No section name is defined.
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
            ?: ['start', 'transition', 'target', 'reachedAt', 'user', 'scope', 'successful'];

        foreach ($history as $state) {
            $row = [];

            foreach ($stateColumns as $column) {
                $row[$column] = $this->renderStateColumn($state, $column);
            }

            $stateId = $state->getStateId();
            assert($stateId !== null);

            $data[$stateId] = $row;
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
     * @return string|array<string,mixed>|null
     */
    public function renderStateColumn(State $state, string $column)
    {
        switch ($column) {
            case 'start':
                return $this->renderStartWorkflowName($state);

            case 'transition':
                return $this->renderTransitionName($state);

            case 'target':
                return [
                    'workflow' => $this->renderTargetWorkflowName($state),
                    'step'     => $this->renderStepName($state),
                ];

            case 'successful':
                $yesNo = $state->isSuccessful() ? 'yes' : 'no';

                return [
                    'label' => $this->trans('MSC.' . $yesNo, [], 'contao_default'),
                    'value' => $state->isSuccessful(),
                ];

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
     */
    private function renderStartWorkflowName(State $state): string
    {
        try {
            return $this->manager->getWorkflowByName($state->getStartWorkflowName())->getLabel();
        } catch (WorkflowException $e) {
            return $state->getStartWorkflowName() ?: '-';
        }
    }

    /**
     * Render the workflow name.
     *
     * @param State $state Workflow item state.
     */
    private function renderTargetWorkflowName(State $state): string
    {
        try {
            return $this->manager->getWorkflowByName($state->getTargetWorkflowName())->getLabel();
        } catch (WorkflowException $e) {
            return $state->getTargetWorkflowName() ?: '-';
        }
    }

    /**
     * Render the transition name.
     *
     * @param State $state Workflow item state.
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
     * @return array<string,mixed>|string|null
     */
    private function renderMetaData(State $state, string $column)
    {
        $data = $state->getData();

        if (! isset($data['metadata']) || ! is_array($data['metadata'])) {
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
     * @param array<string,mixed> $metadata State metadata.
     *
     * @return array<string,mixed>|null
     */
    private function renderUserName(array $metadata): ?array
    {
        if (! isset($metadata['userId'])) {
            return null;
        }

        $userId     = EntityId::fromString($metadata['userId']);
        $repository = $this->entityManager->getRepository($userId->getProviderName());
        $user       = $repository->find($userId->getIdentifier());

        if ($user instanceof Model) {
            $userName = '';

            if ($user::getTable() === 'tl_user') {
                $userName = $user->name;
            } elseif ($user::getTable() === 'tl_member') {
                $userName = $user->firstname . ' ' . $user->lastname;
            }

            $userName = [
                'name'    => $userName,
                'username' => $user->username ?: $user->id,
            ];

            return $userName;
        }

        return $metadata['userId'];
    }

    /**
     * Render the scope.
     *
     * @param array<string,mixed> $metadata State metadata.
     */
    private function renderScope(array $metadata): string
    {
        if (! isset($metadata['scope'])) {
            return '-';
        }

        return $this->trans('history.scope.' . $metadata['scope'], [], 'netzmacht_workflow');
    }
}
