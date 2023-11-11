<?php

namespace WebdevCave\Livewire\DataGrid;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

interface DatagridFilterInterface
{
    /**
     * @return string
     */
    public static function template(string $wire, array $column): string;

    /**
     * @param Builder|EloquentBuilder $builder
     * @param string $fromColumn
     * @param mixed $value
     * @return void
     */
    public static function applyFilter(Builder|EloquentBuilder $builder, string $fromColumn, mixed $value): void;

    /**
     * @return mixed
     */
    public static function defaultValue(): mixed;
}
