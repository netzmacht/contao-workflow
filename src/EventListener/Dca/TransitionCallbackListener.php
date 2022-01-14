<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\Backend;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\Model\Collection;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\ContaoWorkflowBundle\Model\Action\ActionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Step\StepModel;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_filter;
use function array_keys;
use function array_map;
use function assert;
use function sprintf;
use function str_replace;
use function time;

/**
 * Class Transition used for tl_workflow_transition callbacks.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class TransitionCallbackListener extends AbstractListener
{
    use EntityPropertiesTrait;

    /**
     * Table name.
     *
     * @var string
     */
    protected static $name = 'tl_workflow_transition';

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Configuration of available transition types.
     *
     * @var array<array<string,mixed>>
     */
    private $transitionTypes;

    /**
     * Workflow manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Assets manager.
     *
     * @var AssetsManager
     */
    private $assetsManager;

    /**
     * @param DcaManager                 $dcaManager        Data container manager.
     * @param RepositoryManager          $repositoryManager Repository manager.
     * @param WorkflowManager            $workflowManager   Workflow manager.
     * @param TranslatorInterface        $translator        Translator.
     * @param AssetsManager              $assetsManager     Assets manager.
     * @param array<array<string,mixed>> $transitionTypes   Configuration of available transition types.
     */
    public function __construct(
        DcaManager $dcaManager,
        RepositoryManager $repositoryManager,
        WorkflowManager $workflowManager,
        TranslatorInterface $translator,
        AssetsManager $assetsManager,
        array $transitionTypes
    ) {
        parent::__construct($dcaManager);

        $this->repositoryManager = $repositoryManager;
        $this->transitionTypes   = $transitionTypes;
        $this->workflowManager   = $workflowManager;
        $this->translator        = $translator;
        $this->assetsManager     = $assetsManager;
    }

    /**
     * Inject javscript in backend edit mask.
     *
     * @param DataContainer $dataContainer Data container driver.
     */
    public function injectJs(DataContainer $dataContainer): void
    {
        if ($dataContainer->table !== 'tl_workflow_transition' || Input::get('act') !== 'edit') {
            return;
        }

        $this->assetsManager->addJavascript('bundles/netzmachtcontaoworkflow/js/backend.js');
    }

    /**
     * Get available transition types.
     *
     * @return list<string>
     */
    public function getTypes(): array
    {
        return array_keys($this->transitionTypes);
    }

    /**
     * Generate a row view.
     *
     * @param array<string,mixed> $row Current data row.
     */
    public function generateRow(array $row): string
    {
        /** @psalm-suppress PossiblyInvalidCast */
        return sprintf(
            '%s <span class="tl_gray">[%s]</span>',
            $row['label'],
            (string) $this->getFormatter()->formatValue('type', $row['type'])
        );
    }

    /**
     * Get steps which can be a target.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array<string|int,string>
     */
    public function getStepsTo(DataContainer $dataContainer): array
    {
        assert($dataContainer->activeRecord !== null);

        $steps      = [];
        $repository = $this->repositoryManager->getRepository(StepModel::class);
        $collection = $repository->findBy(['.pid=?'], [$dataContainer->activeRecord->pid], ['order' => '.label']);

        if ($collection) {
            foreach ($collection as $model) {
                assert($model instanceof StepModel);

                $steps[$model->id] = $model->label;

                if (! $model->final) {
                    continue;
                }

                $steps[$model->id] .= ' [final]';
            }
        }

        return $steps;
    }

    /**
     * Get entity properties.
     *
     * @return array<string,array<string,string>>
     */
    public function getEntityProperties(): array
    {
        $transition = $this->repositoryManager->getRepository(TransitionModel::class)->find((int) Input::get('id'));
        if (! $transition) {
            return [];
        }

        $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $workflow   = $repository->find((int) $transition->pid);

        if (! $workflow instanceof WorkflowModel) {
            return [];
        }

        return $this->getEntityPropertiesForWorkflow($workflow);
    }

    /**
     * Get all actions.
     *
     * @return array<string,string>
     */
    public function getActions(): array
    {
        $transition = $this->repositoryManager->getRepository(TransitionModel::class)->find((int) Input::get('id'));
        if (! $transition) {
            return [];
        }

        $repository = $this->repositoryManager->getRepository(ActionModel::class);
        $collection = $repository->findBy(['.pid=?'], [$transition->pid], ['.label']);

        return OptionsBuilder::fromCollection($collection, 'label')->getOptions();
    }

    /**
     * Get all conditional transitions.
     *
     * @return array<string,string>
     */
    public function getConditionalTransitions(): array
    {
        $repository = $this->repositoryManager->getRepository(TransitionModel::class);
        $transition = $repository->find((int) Input::get('id'));

        if (! $transition) {
            $collection = $repository->findAll(['order' => '.label']);
        } else {
            $collection = $repository->findBy(
                ['.pid=?', '.id != ?'],
                [$transition->pid, $transition->id],
                ['order' => '.label']
            );
        }

        assert($collection instanceof Collection || $collection === null);

        return OptionsBuilder::fromCollection($collection, 'label')->getOptions();
    }

    /**
     * Load conditional transitions.
     *
     * @param mixed         $value         The actual value.
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return list<string>
     *
     * @throws DBALException If any dbal error occurs.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadConditionalTransitions($value, DataContainer $dataContainer): array
    {
        $statement = $this->repositoryManager
            ->getConnection()
            ->prepare('SELECT tid FROM tl_workflow_transition_conditional_transition WHERE pid=:pid ORDER BY sorting');

        return $statement
            ->executeQuery(['pid' => $dataContainer->id])
            ->fetchFirstColumn();
    }

    /**
     * Save selected conditional transitions.
     *
     * @param mixed         $value         The value.
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return null
     *
     * @throws DBALException When an database related error occurs.
     */
    public function saveConditionalTransitions($value, DataContainer $dataContainer)
    {
        $connection = $this->repositoryManager->getConnection();
        $new        = array_filter(StringUtil::deserialize($value, true));
        $values     = [];
        $statement  = $connection->prepare(
            'SELECT * FROM tl_workflow_transition_conditional_transition WHERE pid=:pid order BY sorting'
        );

        $statement->bindValue('pid', $dataContainer->id);
        $result = $statement->executeQuery();

        while ($row = $result->fetchAssociative()) {
            $values[$row['tid']] = $row;
        }

        $sorting = 0;

        foreach ($new as $conditionalTransitionId) {
            if (! isset($values[$conditionalTransitionId])) {
                $data = [
                    'tstamp'  => time(),
                    'pid'     => $dataContainer->id,
                    'tid'     => $conditionalTransitionId,
                    'sorting' => $sorting,
                ];

                $connection->insert('tl_workflow_transition_conditional_transition', $data);
                $sorting += 128;
            } else {
                if (
                    $values[$conditionalTransitionId]['sorting'] <= ($sorting - 128)
                    || $values[$conditionalTransitionId]['sorting'] >= $sorting + 128
                ) {
                    $connection->update(
                        'tl_workflow_transition_conditional_transition',
                        ['tstamp' => time(), 'sorting' => $sorting],
                        ['id' => $values[$conditionalTransitionId]['id']]
                    );
                }

                $sorting += 128;
                unset($values[$conditionalTransitionId]);
            }
        }

        $ids = array_map(
            static function (array $item): string {
                return $item['id'];
            },
            $values
        );

        if ($ids) {
            $connection->executeStatement(
                'DELETE FROM tl_workflow_transition_conditional_transition WHERE id IN(?)',
                [$ids],
                [Connection::PARAM_INT_ARRAY]
            );
        }

        return null;
    }

    /**
     * Get all workflow options for the workflow transition.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array<string,string>
     */
    public function getWorkflows(DataContainer $dataContainer): array
    {
        if (! $dataContainer->activeRecord) {
            $workflows = $this->workflowManager->getWorkflows();
        } else {
            $workflows     = [];
            $workflowModel = $this->repositoryManager
                ->getRepository(WorkflowModel::class)
                ->find((int) $dataContainer->activeRecord->pid);

            if ($workflowModel) {
                foreach ($this->workflowManager->getWorkflows() as $workflow) {
                    if (
                        $workflow->getName() === 'workflow_' . $workflowModel->id
                        || $workflow->getProviderName() !== $workflowModel->providerName
                    ) {
                        continue;
                    }

                    $workflows[] = $workflow;
                }
            } else {
                $workflows = $this->workflowManager->getWorkflows();
            }
        }

        $options = [];

        foreach ($workflows as $workflow) {
            $options[$workflow->getName()] = $workflow->getLabel();
        }

        return $options;
    }

    /**
     * Generate conditional transition edit button.
     */
    public function conditionalTransitionEditButton(): string
    {
        return sprintf(
            '<a href="javascript:void(0);" class="edit_transition">%s</a>',
            Image::getHtml('edit.svg')
        );
    }

    /**
     * Create edit all button for conditional transitions.
     *
     * @param DataContainer $dataContainer Data container driver.
     */
    public function editAllTransitionsButton(DataContainer $dataContainer): string
    {
        assert($dataContainer->activeRecord !== null);

        // @codingStandardsIgnoreStart
        $template = <<<'html'
<div class="widget edit-all-transitions">
    <div>
        <p>
        <a href="%s" onclick="Backend.openModalIframe({url:this.href, title:'%s'}); return false;" class="tl_submit" id="edit_all_transitions">%s</a>
        </p>
    </div>
</div>
html;
        // @codingStandardsIgnoreEnd

        return sprintf(
            $template,
            Backend::addToUrl('table=tl_workflow_transition&act=&id=' . $dataContainer->activeRecord->pid),
            $this->translator->trans('tl_workflow_transition.editAllTransitions', [], 'contao_tl_workflow_transition'),
            $this->translator->trans('tl_workflow_transition.editAllTransitions', [], 'contao_tl_workflow_transition')
        );
    }

    /**
     * Generate the actions button depending transition type.
     *
     * @param array<string,mixed> $row        The dataset row.
     * @param string              $href       The link target.
     * @param string              $label      The label.
     * @param string              $title      The title.
     * @param string              $icon       The icon.
     * @param string              $attributes Additional html attributes.
     */
    public function generateActionButton(
        array $row,
        string $href,
        string $label,
        string $title,
        string $icon,
        string $attributes
    ): string {
        $supported = ($this->transitionTypes[$row['type']]['actions'] ?? false);
        if ($supported) {
            return sprintf(
                '<a href="%s" title="%s" %s>%s</a> ',
                Backend::addToUrl($href . '&id=' . $row['id']),
                StringUtil::specialchars($label),
                $attributes,
                Image::getHtml($icon, $title)
            );
        }

        return Image::getHtml(str_replace('.', '_.', $icon), $title) . ' ';
    }
}
