<?php

namespace WebdevCave\Livewire\DataGrid\Filters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use WebdevCave\Livewire\DataGrid\DatagridFilterInterface;

class TextFilter implements DatagridFilterInterface
{
    /**
     * @param string $wire
     * @return string
     */
    public static function template(string $wire, array $column): string
    {
        return view(
            'data-grid::'.config('data-grid.theme').'.filters.text',
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
        //$fromColumn = DB::getPdo()->quote($fromColumn);

        if (!empty($value) && is_string($value)) {
            $builder->where(DB::raw("CAST($fromColumn as CHAR)"), 'like', "%$value%");
        }
    }

    public static function defaultValue(): mixed
    {
        return '';
    }
}
