<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Assert\AssertionFailedException;
use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

/**
 * Base renderer class.
 */
abstract class AbstractRenderer implements Renderer
{
    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * The section name to which the renderer renders it's content.
     *
     * @var string
     */
    protected static $section;

    /**
     * Mapping between the content type and the default template.
     *
     * @var array<string,string>
     */
    protected $templates = [];

    /**
     * @param Translator           $translator Translator.
     * @param array<string,string> $templates  Mapping between the content type and the default template.
     *
     * @throws AssertionFailedException If No section name is defined.
     */
    public function __construct(Translator $translator, array $templates = [])
    {
        Assertion::notEmpty(static::$section, 'The renderer section has to be set.');

        $this->translator = $translator;
        $this->templates  = $templates;
    }

    /**
     * Translate a string.
     *
     * @param string              $key        The key which should be translated.
     * @param array<string,mixed> $parameters Optional parameters which has to be replaced.
     * @param string|null         $domain     The language domain.
     */
    public function trans(string $key, array $parameters = [], ?string $domain = null): string
    {
        return $this->translator->trans($key, $parameters, $domain);
    }

    /**
     * Translate a choice.
     *
     * @param string              $key        The key which should be translated.
     * @param int                 $number     The number of the choices.
     * @param array<string,mixed> $parameters Optional parameters which has to be replaced.
     * @param string|null         $domain     The language domain.
     */
    public function transChoice(string $key, int $number, array $parameters = [], ?string $domain = null): string
    {
        $parameters['%count%'] = $number;

        return $this->translator->trans($key, $parameters, $domain);
    }

    public function render(View $view): void
    {
        $section = $this->getSectionName($view);

        if ($view->hasSection($section)) {
            return;
        }

        $view->addSection(
            $section,
            $this->renderParameters($view),
            $this->getDefaultTemplate($view)
        );
    }

    /**
     * Get the section name.
     *
     * @param View $view The workflow item view.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getSectionName(View $view): string
    {
        return static::$section;
    }

    /**
     * Render the parameters.
     *
     * @param View $view The workflow item view.
     *
     * @return array<string,mixed>
     */
    abstract protected function renderParameters(View $view): array;

    /**
     * Get the default template.
     *
     * @param View $view The workflow item view.
     */
    protected function getDefaultTemplate(View $view): ?string
    {
        $contentType = $view->getContentType();

        if (isset($this->templates[$contentType])) {
            return $this->templates[$contentType];
        }

        return null;
    }
}
