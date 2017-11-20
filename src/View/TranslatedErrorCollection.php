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

namespace Netzmacht\Contao\Workflow\View;

use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class TranslatedErrorCollection translated the error identifiers to a localized error message.
 *
 * @package Netzmacht\Contao\Workflow\Data
 */
class TranslatedErrorCollection
{
    /**
     * The error message domain.
     *
     * @var string
     */
    protected $domain = 'contao_workflow_messages';

    /**
     * The error collection.
     *
     * @var ErrorCollection
     */
    private $errorCollection;

    /**
     * The translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Construct.
     *
     * @param ErrorCollection $errorCollection Error collection.
     * @param Translator      $translator      Translator.
     * @param string|null     $domain          Optional error domain.
     */
    public function __construct(ErrorCollection $errorCollection, Translator $translator, ?string $domain = null)
    {
        $this->errorCollection = $errorCollection;
        $this->translator      = $translator;

        if ($domain) {
            $this->domain = $domain;
        }
    }

    /**
     * Get all errors translated as array.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->translateErrorCollection($this->errorCollection);
    }

    /**
     * Translate an error collection.
     *
     * @param ErrorCollection $errorCollection The error collection to craeted.
     *
     * @return array
     */
    private function translateErrorCollection(ErrorCollection $errorCollection)
    {
        $errors = array();

        foreach ($errorCollection as $error) {
            list ($message, $params, $collection) = $error;

            $message = $this->translator->trans($message, $params, $this->domain);

            if ($collection) {
                $collection = $this->translateErrorCollection($collection);
            }

            $errors[] = array($message, $collection);
        }

        return $errors;
    }
}
