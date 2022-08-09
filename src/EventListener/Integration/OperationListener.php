<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Integration;

use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Flow\Exception\StepNotFoundException;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\Routing\RouterInterface as Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function sprintf;

/**
 * Class OperationListener handles the workflow operation button
 */
final class OperationListener
{
    /**
     * The workflow manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * Entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * The router.
     *
     * @var Router
     */
    private $router;

    /**
     * Authorization checker.
     *
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param WorkflowManager               $workflowManager      The workflow manager.
     * @param EntityManager                 $entityManager        Entity manager.
     * @param Router                        $router               The router.
     * @param AuthorizationCheckerInterface $authorizationChecker Authorization checker.
     */
    public function __construct(
        WorkflowManager $workflowManager,
        EntityManager $entityManager,
        Router $router,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->workflowManager      = $workflowManager;
        $this->entityManager        = $entityManager;
        $this->router               = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Handle the workflow operation button callback.
     *
     * @param array<string,mixed> $row        Current record.
     * @param string              $href       The default href.
     * @param string              $label      The button label.
     * @param string              $title      The button title.
     * @param string              $icon       The button icon.
     * @param string              $attributes Additional button attributes.
     * @param string              $table      The table name.
     */
    public function workflowOperationButton(
        array $row,
        string $href,
        string $label,
        string $title,
        string $icon,
        string $attributes,
        string $table
    ): string {
        $entityId   = EntityId::fromProviderNameAndId($table, $row['id']);
        $repository = $this->entityManager->getRepository($table);
        $entity     = $repository->find($entityId->getIdentifier());

        if (! $this->workflowManager->hasWorkflow($entityId, $entity)) {
            return '';
        }

        $item     = $this->workflowManager->createItem($entityId, $entity);
        $stepName = $item->getCurrentStepName();
        if ($stepName !== null) {
            try {
                $workflow = $this->workflowManager->getWorkflow($entityId, $entity);
                $step     = $workflow->getStep($stepName);
            } catch (StepNotFoundException $exception) {
                return '';
            }

            if (! $this->authorizationChecker->isGranted('step:' . $step->getName(), $item)) {
                return '';
            }
        }

        $href = $this->router->generate(
            'netzmacht.contao_workflow.backend.step',
            ['module' => Input::get('do'), 'entityId' => (string) $entityId]
        );

        return sprintf(
            '<a href="%s" title="%s" %s>%s</a> ',
            $href,
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }
}
