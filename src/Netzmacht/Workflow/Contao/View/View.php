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


interface View 
{
    /**
     * @param string $template Template name
     *
     * @return $this
     */
    public function setTemplate($template);

    /**
     * Render the view
     *
     * @return string
     */
    public function render();
}
