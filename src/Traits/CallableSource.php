<?php

namespace WebdevCave\Livewire\DataGrid\Traits;

use InvalidArgumentException;

trait CallableSource
{
    /**
     * @param string|array|null $source
     * @param ...$params
     * @return mixed
     */
    protected function invokeSource(string|array|null $source, ...$params): mixed
    {
        if (empty($source)) {
            throw new InvalidArgumentException("Could not invoke an empty source");
        }

        $this->enforceCallable('source', $source);

        return is_array($source) ? app($source[0])->{$source[1]}(...$params) : $source(...$params);
    }

    /**
     * @param string $propertyName
     * @param string|array|null $value
     * @return void
     */
    protected function enforceCallable(string $propertyName, string|array|null $value): void
    {
        if (
            $value && (
                (is_string($value) && !is_callable($value))
                || (is_array($value) && !method_exists($value[0], $value[1]))
            )
        ) {
            throw new InvalidArgumentException("'$propertyName' must be callable");
        }
    }
}
