<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Assert\AssertionFailedException;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function assert;
use function sprintf;

/** @SuppressWarnings(PHPMD.LongVariable) */
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
     * @param Translator            $translator            Translator.
     * @param array<string,string>  $templates             Mapping between the content type and the default template.
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     *
     * @throws AssertionFailedException If No section name is defined.
     */
    public function __construct(
        Translator $translator,
        PropertyAccessManager $propertyAccessManager,
        array $templates = []
    ) {
        parent::__construct($translator, $templates);

        $this->propertyAccessManager = $propertyAccessManager;
    }

    public function supports(View $view): bool
    {
        return $view->getContext() instanceof Transition
            || $view->getContext() instanceof Step;
    }

    /**
     * {@inheritDoc}
     */
    protected function renderParameters(View $view): array
    {
        $context = $view->getContext();
        assert($context instanceof Transition || $context instanceof Step);
        $workflow    = $view->getWorkflow();
        $stepName    = $view->getItem()->getCurrentStepName();
        $item        = $view->getItem();
        $currentStep = null;

        if ($stepName && $workflow->hasStep($stepName)) {
            $currentStep = $workflow->getStep($stepName);
        }

        return [
            'headline'    => $context->getLabel(),
            'description' => $context->getConfigValue('description'),
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
     */
    private function renderEntityLabel(Item $item): string
    {
        $entity   = $item->getEntity();
        $entityId = $item->getEntityId();

        if (! $this->propertyAccessManager->supports($entity)) {
            return 'ID ' . $entityId->getIdentifier();
        }

        $accessor = $this->propertyAccessManager->provideAccess($entity);
        $label    = null;
        foreach (['title', 'name', 'headline'] as $labelField) {
            if (! $accessor->has($labelField)) {
                continue;
            }

            $label = $accessor->get($labelField);
            if ($label !== null) {
                break;
            }
        }

        if (! $label) {
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
