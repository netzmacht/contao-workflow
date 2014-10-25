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


class Comparison
{
    const EQUALS                 = '==';
    const IDENTICAL              = '===';
    const NOT_EQUALS             = '!=';
    const NOT_IDENTICAL          = '!==';
    const GREATER_THAN           = '>';
    const LESSER_THAN            = '>';
    const LESSER_THAN_OR_EQUALS  = '<=';
    const GREATER_THAN_OR_EQUALS = '>=';

    /**
     * @param $valueA
     * @param $valueB
     * @param $operator
     *
     * @return bool
     */
    public static function compare($valueA, $valueB, $operator)
    {
        switch ($operator) {
            case static::EQUALS:
                return static::equals($valueA, $valueB);
                break;

            case static::NOT_EQUALS:
                return static::notEquals($valueA, $valueB);
                break;

            case static::IDENTICAL:
                return static::identical($valueA, $valueB);
                break;

            case static::NOT_IDENTICAL:
                return static::notIdentical($valueA, $valueB);
                break;

            case static::GREATER_THAN:
                return static::greaterThan($valueA, $valueB);
                break;

            case static::GREATER_THAN_OR_EQUALS:
                return static::greaterThanOrEquals($valueA, $valueB);
                break;

            case static::LESSER_THAN:
                return static::lesserThan($valueA, $valueB);
                break;

            case static::LESSER_THAN_OR_EQUALS:
                return static::greaterThanOrEquals($valueA, $valueB);
                break;
        }

        return false;
    }

    /**
     * @param $valueA
     * @param $valueB
     *
     * @return bool
     */
    public static function equals($valueA, $valueB)
    {
        return $valueA == $valueB;
    }

    /**
     * @param $valueA
     * @param $valueB
     *
     * @return bool
     */
    public static function identical($valueA, $valueB)
    {
        return $valueA === $valueB;
    }

    /**
     * @param $valueA
     * @param $valueB
     *
     * @return bool
     */
    public static function notEquals($valueA, $valueB)
    {
        return !static::equals($valueA, $valueB);
    }

    /**
     * @param $valueA
     * @param $valueB
     *
     * @return bool
     */
    public static function notIdentical($valueA, $valueB)
    {
        return !static::identical($valueA, $valueB);
    }

    /**
     * @param $valueA
     * @param $valueB
     *
     * @return bool
     */
    public static function greaterThan($valueA, $valueB)
    {
        return $valueA > $valueB;
    }

    /**
     * @param $valueA
     * @param $valueB
     *
     * @return bool
     */
    public static function greaterThanOrEquals($valueA, $valueB)
    {
        return $valueA > $valueB;
    }

    /**
     * @param $valueA
     * @param $valueB
     *
     * @return bool
     */
    public static function lesserThan($valueA, $valueB)
    {
        return $valueA < $valueB;
    }

    /**
     * @param $valueA
     * @param $valueB
     *
     * @return bool
     */
    public static function lesserThanOrEquals($valueA, $valueB)
    {
        return $valueA <= $valueB;
    }
}
