<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow;

use ContaoCommunityAlliance\Translator\TranslatorInterface;

class ErrorCollection
{
    const TRANSLATION_DOMAIN = 'workflow_errors';

    /**
     * @var array
     */
    private $errors = array();


    /**
     * @param $errors
     */
    public function __construct(array $errors = array())
    {
        $this->addErrors($errors);
    }

    /**
     * @param $message
     * @param array $params
     * @return $this
     */
    public function addError($message, array $params = array())
    {
        $this->errors[] = array($message, $params);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @return int
     */
    public function countErrors()
    {
        return count($this->errors);
    }

    /**
     * @param $index
     * @return array
     */
    public function getError($index)
    {
        if (isset($this->errors[$index])) {
            return $this->errors[$index];
        }

        throw new \InvalidArgumentException('Error with index "' . $index . '" not set.');
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->errors = array();

        return $this;
    }

    /**
     * @param TranslatorInterface $translator
     * @param string $domain
     * @return array
     */
    public function getTranslatedList(TranslatorInterface $translator, $domain = ErrorCollection::TRANSLATION_DOMAIN)
    {
        $errors = array();

        foreach ($this->errors as $error) {
            $errors[] = $translator->translate($error[0], $domain, $error[1]);
        }

        return $errors;
    }

    /**
     * @param $index
     * @param TranslatorInterface $translator
     * @param string $domain
     * @return string
     */
    public function getTranslated(
        $index,
        TranslatorInterface $translator,
        $domain = ErrorCollection::TRANSLATION_DOMAIN
    ) {
        $error = $this->getError($index);

        return $translator->translate($error[0], $domain, $error[1]);
    }

    /**
     * @param $errors
     * @return $this
     */
    public function addErrors(array $errors)
    {
        foreach ($errors as $error) {
            list($message, $params) = (array) $error;
            $this->addError($message, $params);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
