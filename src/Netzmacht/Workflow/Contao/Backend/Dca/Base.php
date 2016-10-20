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

namespace Netzmacht\Workflow\Contao\Backend\Dca;

use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Netzmacht\Workflow\Contao\ServiceContainerTrait;
use Verraes\ClassFunctions\ClassFunctions;

/**
 * Class Base is a base dca helper class.
 *
 * @package Netzmacht\Workflow\Contao\Backend\Dca
 */
class Base
{
    use ServiceContainerTrait;

    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Default translation domain.
     *
     * @var string
     */
    protected $defaultDomain;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->translator = $this->getServiceContainer()->getTranslator();

        if (!$this->defaultDomain) {
            $this->defaultDomain = 'tl_workflow_' . strtolower(ClassFunctions::short($this));
        }
    }

    /**
     * Translate a string.
     *
     * @param string $name   The string to translate.
     * @param array  $params Translation params.
     * @param null   $domain Translation domain.
     *
     * @return mixed
     */
    public function translate($name, $params = array(), $domain = null)
    {
        if (!$domain) {
            $domain = $this->defaultDomain;
        }

        return $this->translator->translate($name, $domain, $params);
    }
}
