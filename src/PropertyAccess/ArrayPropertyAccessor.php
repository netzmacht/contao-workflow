<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use ArrayAccess;
use ArrayIterator;
use Netzmacht\ContaoWorkflowBundle\Exception\PropertyAccessFailed;
use Traversable;
use function array_key_exists;
use function is_array;

/**
 * Class ArrayPropertyAccessor
 */
final class ArrayPropertyAccessor implements PropertyAccessor
{
    /**
     * Data array.
     *
     * @var array|ArrayAccess
     */
    private $array;

    /**
     * ArrayPropertyAccess constructor.
     *
     * @param array|ArrayAccess $array Data array.
     */
    public function __construct($array)
    {
        $this->array = $array;
    }

    /**
     * {@inheritDoc}
     */
    public static function supports($object): bool
    {
        return is_array($object) || $object instanceof ArrayAccess;
    }

    /**
     * {@inheritDoc}
     */
    public static function create($object): PropertyAccessor
    {
        if (self::supports($object)) {
            return new self($object);
        }

        throw new PropertyAccessFailed('Unsupported data structure.');
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, $value): void
    {
        $this->array[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name)
    {
        return ($this->array[$name] ?? null);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        if ($this->array instanceof ArrayAccess) {
            return $this->array->offsetExists($name);
        }

        return array_key_exists($name, $this->array);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->array);
    }
}
