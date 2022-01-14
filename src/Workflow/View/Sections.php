<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View;

use ArrayIterator;
use IteratorAggregate;
use RuntimeException;

use function array_key_exists;

final class Sections implements IteratorAggregate
{
    /**
     * The generated sections.
     *
     * @var array<string,array<string,string>>
     */
    private $sections;

    /**
     * The section templates.
     *
     * @var array<string,string|null>
     */
    private $templates;

    /**
     * @param array<string,array<string,string>> $sections  The generated sections.
     * @param array<string,string|null>          $templates The section templates.
     */
    public function __construct(array $sections, array $templates)
    {
        $this->sections  = $sections;
        $this->templates = $templates;
    }

    /**
     * Render the sections.
     *
     * @return array<string,string>
     */
    public function get(string $name, bool $remove = true): array
    {
        if (! isset($this->sections[$name])) {
            return [];
        }

        $return = $this->sections[$name];

        if ($remove) {
            unset($this->sections[$name]);
        }

        return $return;
    }

    /**
     * Get the template of a section.
     *
     * @param string $name The section name.
     */
    public function getTemplate(string $name): ?string
    {
        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        }

        return null;
    }

    public function offsetExists(string $offset): bool
    {
        return array_key_exists($offset, $this->sections);
    }

    /**
     * @return array<string,string>|null
     */
    public function offsetGet(string $offset): ?array
    {
        return $this->sections[$offset] ?? null;
    }

    /**
     * @param mixed $value
     *
     * @throws RuntimeException Overriding sections is not allowed.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetSet(string $offset, $value): void
    {
        throw new RuntimeException('Overriding sections is not allowed.');
    }

    public function offsetUnset(string $offset): void
    {
        unset($this->sections[$offset]);
    }

    /**
     * Allow iterating over the sections.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->sections);
    }
}
