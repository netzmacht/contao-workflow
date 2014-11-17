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

class TranslatedErrorCollection
{
    protected $domain = 'workflow_messages';

    /**
     * @var ErrorCollection
     */
    private $errorCollection;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->translateErrorCollection($this->errorCollection);
    }

    /**
     * @param ErrorCollection $errorCollection
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
