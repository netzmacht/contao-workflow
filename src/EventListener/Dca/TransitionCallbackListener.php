<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\Backend;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
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
use Symfony\Component\Translation\TranslatorInterface;
use function array_filter;
use function array_keys;
use function array_map;
use function sprintf;
use function str_replace;
use function time;

/**
 * Class Transition used for tl_workflow_transition callbacks.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Dca
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
     * @var array<array>
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
     * Transition constructor.
     *
     * @param DcaManager          $dcaManager        Data container manager.
     * @param RepositoryManager   $repositoryManager Repository manager.
     * @param WorkflowManager     $workflowManager   Workflow manager.
     * @param TranslatorInterface $translator        Translator.
     * @param AssetsManager       $assetsManager     Assets manager.
     * @param array<array>        $transitionTypes   Configuration of available transition types.
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
     *
     * @return void
     */
    public function injectJs($dataContainer): void
    {
        if ($dataContainer->table === 'tl_workflow_transition' && Input::get('act') === 'edit') {
            $this->assetsManager->addJavascript('bundles/netzmachtcontaoworkflow/js/backend.js');
        }
    }

    /**
     * Get available transition types.
     *
     * @return array<string>
     */
    public function getTypes(): array
    {
        return array_keys($this->transitionTypes);
    }

    /**
     * Generate a row view.
     *
     * @param array $row Current data row.
     *
     * @return string
     */
    public function generateRow(array $row): string
    {
        return sprintf(
            '%s <span class="tl_gray">[%s]</span>',
            $row['label'],
            $this->getFormatter()->formatValue('type', $row['type'])
        );
    }

    /**
     * Get steps which can be a target.
     *
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getStepsTo($dataContainer): array
    {
        $steps      = [];
        $repository = $this->repositoryManager->getRepository(StepModel::class);
        $collection = $repository->findBy(['.pid=?'], [$dataContainer->activeRecord->pid], ['order' => '.label']);

        if ($collection) {
            while ($collection->next()) {
                $steps[$collection->id] = $collection->label;

                if ($collection->final) {
                    $steps[$collection->id] .= ' [final]';
                }
            }
        }

        return $steps;
    }

    /**
     * Get entity properties.
     *
     * @return array
     */
    public function getEntityProperties(): array
    {
        $transition = $this->repositoryManager->getRepository(TransitionModel::class)->find((int) Input::get('id'));
        if (! $transition) {
            return [];
        }

        $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
        $workflow   = $repository->find((int) $transition->pid);

        if (!$workflow instanceof WorkflowModel) {
            return [];
        }

        return $this->getEntityPropertiesForWorkflow($workflow);
    }

    /**
     * Get all actions.
     *
     * @return array
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
     * @return array
     */
    public function getConditionalTransitions(): array
    {
        $repository = $this->repositoryManager->getRepository(TransitionModel::class);
        $transition = $repository->find((int) Input::get('id'));

        if (!$transition) {
            $collection = $repository->findAll(['order' => '.label']);
        } else {
            $collection = $repository->findBy(
                ['.pid=?', '.id != ?'],
                [$transition->pid, $transition->id],
                ['order' => '.label']
            );
        }

        return OptionsBuilder::fromCollection($collection, 'label')->getOptions();
    }

    /**
     * Load conditional transitions.
     *
     * @param mixed          $value         The actual value.
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @throws DBALException If any dbal error occurs.
     */
    public function loadConditionalTransitions($value, $dataContainer)
    {
        $statement = $this->repositoryManager
            ->getConnection()
            ->prepare('SELECT tid FROM tl_workflow_transition_conditional_transition WHERE pid=:pid ORDER BY sorting');

        if ($statement->execute(['pid' => $dataContainer->id])) {
            return $statement->fetchAll(\PDO::FETCH_COLUMN, 0);
        }

        return [];
    }

    /**
     * Save selected conditional transitions.
     *
     * @param mixed         $value         The value.
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return null
     * @throws DBALException When an database related error occurs.
     */
    public function saveConditionalTransitions($value, $dataContainer)
    {
        $connection = $this->repositoryManager->getConnection();
        $new        = array_filter(StringUtil::deserialize($value, true));
        $values     = [];
        $statement  = $connection->prepare(
            'SELECT * FROM tl_workflow_transition_conditional_transition WHERE pid=:pid order BY sorting'
        );

        $statement->bindValue('pid', $dataContainer->id);
        $statement->execute();

        while ($row = $statement->fetch()) {
            $values[$row['tid']] = $row;
        }

        $sorting = 0;

        foreach ($new as $conditionalTransitionId) {
            if (!isset($values[$conditionalTransitionId])) {
                $data = [
                    'tstamp'  => time(),
                    'pid'     => $dataContainer->id,
                    'tid'     => $conditionalTransitionId,
                    'sorting' => $sorting,
                ];

                $connection->insert('tl_workflow_transition_conditional_transition', $data);
                $sorting += 128;
            } else {
                if ($values[$conditionalTransitionId]['sorting'] <= ($sorting - 128)
                    || $values[$conditionalTransitionId]['sorting'] >= ($sorting + 128)
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
            function ($item) {
                return $item['id'];
            },
            $values
        );

        if ($ids) {
            $connection->executeUpdate(
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
     * @return array
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
                    if ($workflow->getName() === 'workflow_' . $workflowModel->id
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
     *
     * @return string
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
     *
     * @return string
     */
    public function editAllTransitionsButton($dataContainer): string
    {
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
     * @param array       $row        The dataset row.
     * @param string|null $href       The link target.
     * @param string      $label      The label.
     * @param string      $title      The title.
     * @param string|null $icon       The icon.
     * @param string      $attributes Additional html attributes.
     *
     * @return string
     */
    public function generateActionButton(
        array $row,
        ?string $href,
        string $label,
        string $title,
        ?string $icon,
        string $attributes
    ): string {
        $supported = ($this->transitionTypes[$row['type']]['actions'] ?? false);
        if ($supported) {
            return sprintf(
                '<a href="%s" title="%s" %s>%s</a> ',
                Backend::addToUrl($href),
                StringUtil::specialchars($label),
                $attributes,
                Image::getHtml($icon, $title)
            );
        }

        return Image::getHtml(str_replace('.', '_.', $icon), $title) . ' ';
    }
}
