<?php

namespace WebdevCave\Livewire\DataGrid;

use WebdevCave\Livewire\DataGrid\Traits\CallableSource;

class DatagridSettings
{
    use CallableSource;

    private array $columns = [];
    private array|string $queryProvider;
    private array|string|null $actions = null;
    private bool $hasFilters = false;

    /**
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @return $this
     */
    public function addColumn(
        string|null $from,
        string $label,
        bool $sorting = true,
        string|array|null $renderer = null,
        string $width = 'auto',
        string|null $filter = null
    ): static {
        $this->enforceCallable('renderer', $renderer);

        $this->columns[] = compact('from', 'label', 'sorting', 'renderer', 'width', 'filter');

        if (!empty($filter)) {
            $this->hasFilters = true;
        }

        return $this;
    }

    /**
     * @param array|string $queryProvider
     * @return $this
     */
    public function setQueryProvider(array|string $queryProvider): static
    {
        $this->enforceCallable('queryProvider', $queryProvider);
        $this->queryProvider = $queryProvider;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'actions' => $this->actions,
            'columns' => $this->columns,
            'queryProvider' => $this->queryProvider,
            'hasFilters' => $this->hasFilters,
        ];
    }
}
