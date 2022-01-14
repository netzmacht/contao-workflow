<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Netzmacht\ContaoWorkflowBundle\Exception\PropertyAccessFailed;
use Netzmacht\ContaoWorkflowBundle\Exception\RuntimeException;
use Traversable;

use function array_key_exists;
use function is_array;

final class ArrayPropertyAccessor implements PropertyAccessor
{
    /**
     * Data array.
     *
     * @var array<mixed,mixed>|ArrayAccess<mixed,mixed>
     */
    private $array;

    /**
     * @param array<mixed,mixed>|ArrayAccess<mixed,mixed> $array Data array.
     */
    public function __construct($array)
    {
        $this->array = $array;
    }

    /**
     * @deprecated Will be removed in the next major release. use ArrayPropertyAccessorFactory instead.
     *
     * @param mixed $object
     */
    public static function supports($object): bool
    {
        return is_array($object) || $object instanceof ArrayAccess;
    }

    /**
     * @deprecated Will be removed in the next major release. use ArrayPropertyAccessorFactory instead.
     *
     * @param mixed $object
     *
     * @throws PropertyAccessFailed When data structure is not supported.
     */
    public static function create($object): PropertyAccessor
    {
        /** @psalm-suppress DeprecatedMethod */
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
        return $this->array[$name] ?? null;
    }

    public function has(string $name): bool
    {
        if ($this->array instanceof ArrayAccess) {
            return $this->array->offsetExists($name);
        }

        return array_key_exists($name, $this->array);
    }

    /** @return Traversable<mixed,mixed> */
    public function getIterator(): Traversable
    {
        if (is_array($this->array)) {
            return new ArrayIterator($this->array);
        }

        if ($this->array instanceof IteratorAggregate) {
            return $this->array->getIterator();
        }

        if ($this->array instanceof Traversable) {
            return $this->array;
        }

        throw new RuntimeException('Unable to create iterator for ArrayAccess object');
    }
}
