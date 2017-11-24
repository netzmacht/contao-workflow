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

namespace Netzmacht\Contao\Workflow\EventListener\Dca;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class Base is a base dca helper class.
 *
 * @package Netzmacht\Contao\Workflow\Backend\Dca
 */
trait TranslatePlugin
{
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
     * Translate a string.
     *
     * @param string      $name   The string to translate.
     * @param array       $params Translation params.
     * @param string|null $domain Translation domain.
     *
     * @return string
     */
    public function translate(string $name, array $params = array(), ?string $domain = null): string
    {
        return $this->translator->trans($name, $params, $domain ?: $this->defaultDomain);
    }
}
