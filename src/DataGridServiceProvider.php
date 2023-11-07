<?php

namespace WebdevCave\Livewire\DataGrid;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use WebdevCave\Livewire\DataGrid\Filters\DateRangeFilter;
use WebdevCave\Livewire\DataGrid\Filters\TextFilter;
use WebdevCave\Livewire\DataGrid\Livewire\DataGrid;

class DataGridServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/data-grid.php', 'data-grid');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'data-grid');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'data-grid');
        Livewire::component('data-grid', DataGrid::class);

        $this->publishes([
            __DIR__.'/../config/data-grid.php' => $this->app->configPath('data-grid.php'),
            __DIR__.'/../lang' => $this->app->langPath('vendor/data-grid'),
        ]);
//lang_path('');
        $this->registerDefaultFilters();
    }

    private function registerDefaultFilters(): void
    {
        DataGrid::registerFilter('text', TextFilter::class);
        DataGrid::registerFilter('date-range', DateRangeFilter::class);
    }
}