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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class TransitionTeaserRenderer
 */
final class TeaserRenderer extends AbstractRenderer
{
    /**
     * Section name.
     *
     * @var string
     */
    protected static $section = 'teaser';

    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * AbstractRenderer constructor.
     *
     * @param Translator            $translator            Translator.
     * @param array                 $templates             Mapping between the content type and the default template.
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     *
     * @throws \Assert\AssertionFailedException If No section name is defined.
     */
    public function __construct(
        Translator $translator,
        PropertyAccessManager $propertyAccessManager,
        array $templates = []
    ) {
        parent::__construct($translator, $templates);

        $this->propertyAccessManager = $propertyAccessManager;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(View $view): bool
    {
        return $view->getContext() instanceof Transition
            || $view->getContext() instanceof Step
            || $view->getContext() === null;
    }

    /**
     * {@inheritDoc}
     */
    protected function renderParameters(View $view): array
    {
        /** @var Transition|Step $context */
        $context     = $view->getContext();
        $workflow    = $view->getWorkflow();
        $stepName    = $view->getItem()->getCurrentStepName();
        $item        = $view->getItem();
        $currentStep = null;

        if ($stepName && $workflow->hasStep($stepName)) {
            $currentStep = $workflow->getStep($stepName);
        }

        return [
            'headline'    => $context ? $context->getLabel() : $workflow->getLabel(),
            'description' => $context ? $context->getConfigValue('description') : null,
            'workflow'    => $view->getWorkflow(),
            'currentStep' => $currentStep,
            'item'        => $view->getItem(),
            'entityLabel' => $this->renderEntityLabel($item),
        ];
    }

    /**
     * Render the data record label.
     *
     * @param Item $item The current item.
     *
     * @return string|null
     */
    private function renderEntityLabel(Item $item): ?string
    {
        $entity   = $item->getEntity();
        $entityId = $item->getEntityId();

        if (! $this->propertyAccessManager->supports($entity)) {
            return 'ID ' . $entityId->getIdentifier();
        }

        $accessor = $this->propertyAccessManager->provideAccess($entity);
        $label    = null;
        foreach (['title', 'name', 'headline'] as $labelField) {
            if (!$accessor->has($labelField)) {
                continue;
            }

            $label = $accessor->get($labelField);
            if ($label !== null) {
                break;
            }
        }

        if (!$label) {
            $label = 'ID ' . $entityId->getIdentifier();
        } else {
            $label = sprintf('%s (ID %s)', $label, $entityId->getIdentifier());
        }

        $providerKey   = 'MOD.' . $item->getEntityId()->getProviderName();
        $providerLabel = $this->trans($providerKey, [], 'contao_modules');
        if ($providerLabel === $providerKey) {
            return $label;
        }

        return sprintf('%s: %s', $providerLabel, $label);
    }
}
