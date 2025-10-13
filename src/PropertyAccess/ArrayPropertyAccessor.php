<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Netzmacht\ContaoWorkflowBundle\Exception\PropertyAccessFailed;
use Netzmacht\ContaoWorkflowBundle\Exception\RuntimeException;
use Override;
use Traversable;

use function array_key_exists;
use function is_array;

final class ArrayPropertyAccessor implements PropertyAccessor
{
    /** @param ArrayAccess<mixed,mixed>|array<mixed,mixed> $array Data array. */
    public function __construct(private array|ArrayAccess $array)
    {
    }

    /** @deprecated Will be removed in the next major release. use ArrayPropertyAccessorFactory instead. */
    public static function supports(mixed $object): bool
    {
        return is_array($object) || $object instanceof ArrayAccess;
    }

    /**
     * @deprecated Will be removed in the next major release. use ArrayPropertyAccessorFactory instead.
     *
     * @throws PropertyAccessFailed When data structure is not supported.
     */
    public static function create(mixed $object): PropertyAccessor
    {
        /** @psalm-suppress DeprecatedMethod */
        if (self::supports($object)) {
            return new self($object);
        }

        throw new PropertyAccessFailed('Unsupported data structure.');
    }

     /** {@inheritDoc} */
    #[Override]
    public function set(string $name, mixed $value): void
    {
        $this->array[$name] = $value;
    }

     /** {@inheritDoc} */
    #[Override]
    public function get(string $name): mixed
    {
        return $this->array[$name] ?? null;
    }

    #[Override]
    public function has(string $name): bool
    {
        if ($this->array instanceof ArrayAccess) {
            return $this->array->offsetExists($name);
        }

        return array_key_exists($name, $this->array);
    }

    /** {@inheritDoc} */
    #[Override]
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
