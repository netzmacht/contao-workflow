<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Form;

use ContaoCommunityAlliance\Translator\TranslatorInterface;

/**
 * Form type with the translator being available.
 *
 * @package Netzmacht\Workflow\Contao\Form
 */
class LocalizedFormType extends FormType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Construct.
     *
     * @param TranslatorInterface $translator
     * @param null                $name
     * @param null                $prefix
     */
    function __construct(TranslatorInterface $translator, $name, $prefix = null)
    {
        $this->translator = $translator;

        parent::__construct($name, $prefix);
    }
}
