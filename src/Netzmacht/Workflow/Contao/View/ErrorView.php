<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\View;


use Netzmacht\Workflow\Contao\Data\TranslatedErrorCollection;

class ErrorView implements View
{
    /**
     * @var TranslatedErrorCollection
     */
    private $translatedErrorCollection;

    /**
     * @var int $depth
     */
    private $depth = 3;

    /**
     * @var string
     */
    private $template = 'workflow_errors';

    /**
     * @param $translatedErrorCollection
     */
    function __construct($translatedErrorCollection)
    {
        $this->translatedErrorCollection = $translatedErrorCollection;
    }

    /**
     * @param string $template Template name
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param int $depth
     *
     * @return $this
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Render the view
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
