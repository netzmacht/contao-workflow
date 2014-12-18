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

namespace Netzmacht\Workflow\Contao\View;

/**
 * Interface View describes workflow related view elements which can be used to display some informations.
 *
 * @package Netzmacht\Workflow\Contao\View
 */
interface View
{
    /**
     * Set the template name.
     *
     * @param string $template Template name.
     *
     * @return $this
     */
    public function setTemplate($template);

    /**
     * Get the template name.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Render the view.
     *
     * @return string
     */
    public function render();
}
