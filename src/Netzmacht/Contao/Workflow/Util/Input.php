<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Util;

use ContaoCommunityAlliance\DcGeneral\InputProviderInterface as InputProvider;

class Input
{
    const PARAMETER        = 'parameter';
    const VALUE            = 'value';
    const PERSISTENT_VALUE = 'persistent';

    /**
     * @param InputProvider $inputProvider
     * @param               $name
     * @param string        $method
     * @param bool          $raw
     *
     * @return mixed|null
     */
    public static function get(InputProvider $inputProvider, $name, $method = Input::PARAMETER, $raw = false)
    {
        switch ($method) {
            case static::PARAMETER:
                return $inputProvider->getParameter($name, $raw);
                break;

            case static::PERSISTENT_VALUE:
                return $inputProvider->getPersistentValue($name);
                break;

            case static::VALUE:
                return $inputProvider->getValue($name, $raw);
                break;
        }

        return null;
    }

    /**
     * @param InputProvider $inputProvider
     * @param               $name
     * @param               $value
     * @param string        $method
     */
    public static function set(InputProvider $inputProvider, $name, $value, $method = Input::PARAMETER)
    {
        switch ($method) {
            case static::PARAMETER:
                $inputProvider->setParameter($name, $value);
                break;

            case static::PERSISTENT_VALUE:
                $inputProvider->setPersistentValue($name, $value);
                break;

            case static::VALUE:
                $inputProvider->setValue($name, $name);
                break;
        }
    }
}
