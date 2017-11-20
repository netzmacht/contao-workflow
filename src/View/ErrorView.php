<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\View;

/**
 * Class ErrorView renders the error collection.
 *
 * @package Netzmacht\Contao\Workflow\View
 */
class ErrorView implements View
{
    /**
     * The translated error collection.
     *
     * @var TranslatedErrorCollection
     */
    private $translatedErrorCollection;

    /**
     * Default depth for reading from the error collection.
     *
     * @var int
     */
    private $depth = 3;

    /**
     * Template name.
     *
     * @var string
     */
    private $template = 'workflow_errors';

    /**
     * Construct.
     *
     * @param TranslatedErrorCollection $translatedErrorCollection The error collection being displayed.
     */
    public function __construct(TranslatedErrorCollection $translatedErrorCollection)
    {
        $this->translatedErrorCollection = $translatedErrorCollection;
    }

    /**
     * Set the template name.
     *
     * @param string $template Template name.
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get the template name.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get the depth.
     *
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Set the depth.
     *
     * @param int $depth The new depth value.
     *
     * @return $this
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Render the view.
     *
     * @return string
     */
    public function render()
    {
        $template         = new \BackendTemplate($this->template);
        $template->errors = $this->translatedErrorCollection->getErrors();
        $template->depth  = $this->depth;

        return $template->parse();
    }
}
