<?php

namespace WebdevCave\Livewire\DataGrid\Filters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use WebdevCave\Livewire\DataGrid\DatagridFilterInterface;

class DateRangeFilter implements DatagridFilterInterface
{
    /**
     * @param string $wire
     * @return string
     */
    public static function template(string $wire, array $column): string
    {
        return view(
            'data-grid::'.config('data-grid.theme').'.filters.date-range',
            compact('wire', 'column')
        )->render();
    }

    /**
     * @param EloquentBuilder|Builder $builder
     * @param string $fromColumn
     * @param mixed $value
     * @return void
     */
    public static function applyFilter(EloquentBuilder|Builder $builder, string $fromColumn, mixed $value): void
    {
        if (!empty($value['from']) && !empty($value['to'])) {
            $builder->whereBetween($fromColumn, [$value['from'], $value['to']]);
        }
    }

    /**
     * @return mixed
     */
    public static function defaultValue(): mixed
    {
        return ['from' => '', 'to' => ''];
    }
}
