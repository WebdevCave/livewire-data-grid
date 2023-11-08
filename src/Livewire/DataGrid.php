<?php

namespace WebdevCave\Livewire\DataGrid\Livewire;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use WebdevCave\Livewire\DataGrid\DatagridFilterInterface;
use WebdevCave\Livewire\DataGrid\DatagridSettings;
use WebdevCave\Livewire\DataGrid\Traits\CallableSource;

class DataGrid extends Component
{
    use CallableSource;
    use WithPagination {
        setPage as traitSetPage;
    }

    protected static ?string $defaultTemplate = null;
    protected static array $filterClasses = [];
    private static array $currentFilters = [];

    #[Url]
    public array $filter = [];

    #[Url]
    public array $sorting = [];

    public array $rowsOptions = [5,10,20,50,100];

    #[Url]
    public int $page = 1;

    #[Url]
    public int $rowsPerPage = 0;

    public ?string $template = null;

    public array $settings = [];

    /**
     * @return void
     */
    public function clearFilters(): void
    {
        foreach ($this->settings['columns'] as $column) {
            if (empty($column['from'])) {
                continue;
            }

            $defaultFilter = '';
            if ($column['filter']) {
                $defaultFilter = [self::$filterClasses[$column['filter']], 'defaultValue']();
            }

            $this->filter[$column['from']] = $defaultFilter;
        }
    }

    /**
     * @return void
     */
    public function clearSorting(): void
    {
        foreach ($this->settings['columns'] as $column) {
            if (empty($column['from'])) {
                continue;
            }

            $this->sorting[$column['from']] = '';
        }
    }

    /**
     * @return void
     */
    public function mount(string|array $settings = []): void
    {
        $request = request();

        $settings = $this->invokeSource($settings);
        if (!($settings instanceof DatagridSettings)) {
            $settingsClass = DatagridSettings::class;
            throw new InvalidArgumentException("Source must return a '$settingsClass' intance");
        }

        $this->settings = $settings->toArray();
        $this->clearFilters();
        $this->clearSorting();

        $defaultRowsPerPage = Arr::first($this->rowsOptions, default: 5);
        $this->rowsPerPage = $request->query('rowsPerPage', $defaultRowsPerPage);
        if (!in_array($this->rowsPerPage, $this->rowsOptions)) {
            $this->rowsPerPage = $defaultRowsPerPage;
        }

        $this->page = $request->query('page', 1);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function render(): mixed
    {
        $this->checkForColumnInjection();
        $this->loadCurrentFilters();

        /* @var $pagination LengthAwarePaginator */
        $pagination = $this->settings['queryProvider'](function($query) {
            self::applyFilters($query, $this->filter);
            self::applySorting($query, $this->sorting);
        }, $this->rowsPerPage);

        if (!($pagination instanceof LengthAwarePaginator)) {
            throw new Exception('The query provider must return a '.LengthAwarePaginator::class);
        }

        $filterClasses = self::$filterClasses;

        $template = $this->template ?? self::$defaultTemplate ?? 'data-grid::'.config('data-grid.theme').'.table';
        return view($template, compact('pagination', 'filterClasses'));
    }

    public function setPage($page, $pageName = 'page')
    {
        $this->page = $page;
        $this->traitSetPage($page, $pageName);
    }

    /**
     * @return void
     */
    public function updatedRowsPerPage()
    {
        $this->setPage(1);
    }

    /**
     * @return void
     */
    public function updatedFilter()
    {
        $this->setPage(1);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function checkForColumnInjection(): void
    {
        $trustedColumns = array_column($this->settings['columns'], 'from');
        $untrustedColumns = array_map(function($v) {
            if (!is_string($v)) {
                return $v;
            }

            return explode('.', $v)[0];
        }, array_keys($this->sorting), array_keys($this->filter));

        $differences = array_diff($untrustedColumns, $trustedColumns);
        if (!empty($differences)) {
            throw new Exception('Security breach detected');
        }
    }

    /**
     * @return void
     */
    private function loadCurrentFilters()
    {
        self::$currentFilters = [];

        foreach ($this->settings['columns'] as $column) {
            if (isset(self::$filterClasses[$column['filter']])){
                self::$currentFilters[$column['from']] = self::$filterClasses[$column['filter']];
            }
        }
    }

    /**
     * @param Builder|EloquentBuilder $query
     * @param array $filters
     *
     * @return void
     */
    private static function applyFilters(Builder|EloquentBuilder $query, array $filters): void
    {
        foreach (array_filter($filters) as $column => $value) {
            if (isset(self::$currentFilters[$column])) {
                [self::$currentFilters[$column], 'applyFilter']($query, $column, $value);
            }
        }
    }

    /**
     * @param Builder|EloquentBuilder $query
     * @param array $sorting
     *
     * @return void
     */
    private static function applySorting(Builder|EloquentBuilder $query, array $sorting): void
    {
        foreach ($sorting as $column => $direction) {
            if (empty($direction) || !in_array($direction, ['asc', 'desc'])) {
                continue;
            }

            $query->orderBy($column, $direction);
        }
    }

    /**
     * @param string $slug
     * @param string $className
     * @return void
     */
    public static function registerFilter(string $slug, string $className): void
    {
        if (!is_a($className, DatagridFilterInterface::class, true)) {
            throw new InvalidArgumentException(
                "Invalid class. $className does not implements DatagridFilterInterface"
            );
        }

        self::$filterClasses[$slug] = $className;
    }

    /**
     * @param string $template
     * @return void
     */
    public static function setDefaultTemplate(string $template): void
    {
        self::$defaultTemplate = $template;
    }
}
