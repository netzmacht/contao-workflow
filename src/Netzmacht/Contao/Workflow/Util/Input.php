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

/**
 * Class Input is an util class for handling fetching and settings value from a input provider.
 *
 * It allows switching between parameters, values and persistent values by passing an argument.
 *
 * @package Netzmacht\Contao\Workflow\Util
 */
class Input
{
    const PARAMETER        = 'parameter';
    const VALUE            = 'value';
    const PERSISTENT_VALUE = 'persistent';

    /**
     * Get a value from the input provider.
     *
     * @param InputProvider $inputProvider The input provider.
     * @param string        $name          The name of the property.
     * @param string        $type          The access type.
     * @param bool          $raw           Get the value as raw value.
     *
     * @return mixed|null
     */
    public static function get(InputProvider $inputProvider, $name, $type = self::PARAMETER, $raw = false)
    {
        switch ($type) {
            case static::PERSISTENT_VALUE:
                return $inputProvider->getPersistentValue($name);

            case static::VALUE:
                return $inputProvider->getValue($name, $raw);

            case static::PARAMETER:
            default:
                return $inputProvider->getParameter($name, $raw);
        }
    }

    /**
     * Set a value of the input provider.
     *
     * @param InputProvider $inputProvider The input provider.
     * @param string        $name          The name of the property.
     * @param mixed         $value         The value of the property.
     * @param string        $type          The property type.
     *
     * @return void
     */
    public static function set(InputProvider $inputProvider, $name, $value, $type = self::PARAMETER)
    {
        switch ($type) {
            case static::PERSISTENT_VALUE:
                $inputProvider->setPersistentValue($name, $value);
                break;

            case static::VALUE:
                $inputProvider->setValue($name, $name);
                break;

            case static::PARAMETER:
            default:
                $inputProvider->setParameter($name, $value);
                break;
        }
    }
}
