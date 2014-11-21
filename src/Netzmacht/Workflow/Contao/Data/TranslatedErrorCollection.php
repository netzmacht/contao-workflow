<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Data;

use ContaoCommunityAlliance\Translator\TranslatorInterface as Translator;
use Netzmacht\Workflow\Data\ErrorCollection;

/**
 * Class TranslatedErrorCollection translated the error identifiers to a localized error message.
 *
 * @package Netzmacht\Workflow\Contao\Data
 */
class TranslatedErrorCollection
{
    /**
     * The error message domain.
     *
     * @var string
     */
    protected $domain = 'workflow_messages';

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

            $message = $this->translator->translate($message, $this->domain, $params);

            if ($collection) {
                $collection = $this->translateErrorCollection($collection);
            }

            $errors[] = array($message, $collection);
        }

        return $errors;
    }
}
