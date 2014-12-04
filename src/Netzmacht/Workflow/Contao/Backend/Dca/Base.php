<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Backend\Dca;


use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Verraes\ClassFunctions\ClassFunctions;

class Base
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    protected $defaultDomain;

    function __construct()
    {
        $this->translator    = $GLOBALS['container']['workflow.translator'];

        if (!$this->defaultDomain) {
            $this->defaultDomain = 'tl_workflow_' . strtolower(ClassFunctions::short($this));
        }
    }

    /**
     * @param       $name
     * @param array $params
     * @param null  $domain
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
