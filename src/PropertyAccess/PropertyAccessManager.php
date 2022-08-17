<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use Assert\Assert;
use Netzmacht\ContaoWorkflowBundle\Exception\PropertyAccessFailed;

use function array_key_exists;
use function is_object;
use function spl_object_hash;

final class PropertyAccessManager
{
    /**
     * Property access factories.
     *
     * @var PropertyAccessorFactory[]
     */
    private $factories = [];

    /**
     * Cache of mapped property accessors.
     *
     * @var array<string,PropertyAccessor>
     */
    private $mapping = [];

    /**
     * @param iterable<PropertyAccessorFactory> $factories Property access factories.
     */
    public function __construct(iterable $factories)
    {
        Assert::thatAll($factories)->subclassOf(PropertyAccessorFactory::class);

        foreach ($factories as $factory) {
            $this->factories[] = $factory;
        }
    }

    /**
     * Determine if property access is supported for given entity.
     *
     * @param mixed $data Given data structure.
     */
    public function supports($data): bool
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Provide access to a given data structure.
     *
     * @param mixed $data Data structure.
     *
     * @throws PropertyAccessFailed If no supported data structure is given.
     */
    public function provideAccess($data): PropertyAccessor
    {
        $hash = is_object($data) ? spl_object_hash($data) : null;

        if ($hash && array_key_exists($hash, $this->mapping)) {
            return $this->mapping[$hash];
        }

        foreach ($this->factories as $factory) {
            if (! $factory->supports($data)) {
                continue;
            }

            $accessor = $factory->create($data);

            if ($hash) {
                $this->mapping[$hash] = $accessor;
            }

            return $accessor;
        }

        throw new PropertyAccessFailed('Could not determine property accessor for given data.');
    }
}
