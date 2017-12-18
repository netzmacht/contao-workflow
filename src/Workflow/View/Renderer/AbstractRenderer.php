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

use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Symfony\Component\Translation\TranslatorInterface as Translator;

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
     * @var array
     */
    protected $templates = [];

    /**
     * AbstractRenderer constructor.
     *
     * @param Translator $translator Translator.
     * @param array      $templates  Mapping between the content type and the default template.
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
     * @param string      $key        The key which should be translated.
     * @param array       $parameters Optional parameters which has to be replaced.
     * @param string|null $domain     The language domain.
     *
     * @return string
     */
    public function trans(string $key, array $parameters = [], string $domain = null)
    {
        return $this->translator->trans($key, $parameters, $domain);
    }

    /**
     * Translate a choice.
     *
     * @param string      $key        The key which should be translated.
     * @param int         $number     The number of the choices.
     * @param array       $parameters Optional parameters which has to be replaced.
     * @param string|null $domain     The language domain.
     *
     * @return string
     */
    public function transChoice(string $key, int $number, array $parameters = [], string $domain = null)
    {
        return $this->translator->transChoice($key, $number, $parameters, $domain);
    }

    /**
     * {@inheritdoc}
     */
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
     * @return string
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
     * @return array
     */
    abstract protected function renderParameters(View $view): array;

    /**
     * Get the default template.
     *
     * @param View $view The workflow item view.
     *
     * @return null|string
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
