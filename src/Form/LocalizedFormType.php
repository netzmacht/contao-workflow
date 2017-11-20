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

namespace Netzmacht\Contao\Workflow\Form;

use ContaoCommunityAlliance\Translator\TranslatorInterface;

/**
 * Form type with the translator being available.
 *
 * @package Netzmacht\Contao\Workflow\Form
 */
class LocalizedFormType extends FormType
{
    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Construct.
     *
     * @param TranslatorInterface $translator The translator.
     * @param string              $name       The form type name.
     * @param string|null         $prefix     The prefix.
     */
    public function __construct(TranslatorInterface $translator, $name, $prefix = null)
    {
        $this->translator = $translator;

        parent::__construct($name, $prefix);
    }
}
