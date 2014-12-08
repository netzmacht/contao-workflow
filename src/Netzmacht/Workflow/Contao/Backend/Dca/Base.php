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
use Netzmacht\Workflow\Contao\ServiceContainerTrait;
use Verraes\ClassFunctions\ClassFunctions;

class Base
{
    use ServiceContainerTrait;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    protected $defaultDomain;

    /**
     *
     */
    function __construct()
    {
        $this->translator = $this->getService('workflow.translator');

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
