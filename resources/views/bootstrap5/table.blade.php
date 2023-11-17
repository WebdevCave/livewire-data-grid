<div class="data-grid container-fluid m-0 p-0 position-relative">
    {{ $template }}
    <div class="table-responsive">
        <table class="table table-hover">
            <colgroup>
                @foreach($settings['columns'] as $column)
                    <col style="width: {{ $column['width'] }}"/>
                @endforeach
            </colgroup>
            <thead>
            <tr>
                <td colspan="{{ count($settings['columns']) }}" class="text-end">
                    @if($settings['actions'])
                        {!! $settings['actions']() !!}
                    @endif

                    <button class="btn btn-sm btn-secondary" wire:click="clearFilters" type="button">
                        <i class="bi bi-x-lg"></i>
                        {{ __('data-grid::data-grid.actions.clear-filters') }}
                    </button>

                    <button class="btn btn-sm btn-secondary" wire:click="clearSorting" type="button">
                        <i class="bi bi-x-lg"></i>
                        {{ __('data-grid::data-grid.actions.clear-sorting') }}
                    </button>
                </td>
            </tr>
            <tr>
                @foreach($settings['columns'] as $column)
                    <th class="align-text-top">
                        <span class="text-truncate mb-2 d-block">{{ $column['label'] }}</span>
                        @if ($column['from'] && $column['sorting'])
                            <div class="dropdown">
                                <a class="btn btn-sm btn-outline-secondary dropdown-toggle w-100"
                                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i @class([
                                        "bi",
                                        "bi-chevron-expand" => $sorting[$column['from']] == '',
                                        "bi-chevron-up" => $sorting[$column['from']] == 'asc',
                                        "bi-chevron-down" => $sorting[$column['from']] == 'desc',
                                    ])></i>
                                    {{ __('data-grid::data-grid.sort.change-btn') }}
                                </a>

                                <ul class="dropdown-menu px-1">
                                    <li class="dropdown-item">
                                        <label class="form-check-label">
                                            <input type="radio" value=""
                                                   wire:model.change="sorting.{{ $column['from'] }}"
                                                   class="form-check-input"/>
                                            {{ __('data-grid::data-grid.sort.default') }}
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="form-check-label">
                                            <input type="radio" value="asc"
                                                   wire:model.change="sorting.{{ $column['from'] }}"
                                                   class="form-check-input"/>
                                            {{ __('data-grid::data-grid.sort.ascending') }}
                                        </label>
                                    </li>
                                    <li class="dropdown-item">
                                        <label class="form-check-label">
                                            <input type="radio" value="desc"
                                                   wire:model.change="sorting.{{ $column['from'] }}"
                                                   class="form-check-input"/>
                                            {{ __('data-grid::data-grid.sort.descending') }}
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </th>
                @endforeach
            </tr>
            @if($settings['hasFilters'])
                <tr style="vertical-align: top">
                    @foreach($settings['columns'] as $column)
                        <td>
                            @if($column['filter'] && $column['from'])
                                {!! [$filterClasses[$column['filter']], 'template']('filters.'.$column['from'], $column) !!}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endif
            </thead>

            <tbody>
            @forelse($pagination as $row)
                <tr>
                    @foreach($settings['columns'] as $column)
                        @empty($column['renderer'])
                            <td>{{ $row->{$column['from']} }}</td>
                        @else
                            <td>{!! $column['renderer']($row) !!}</td>
                        @endempty
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">{{ __('data-grid::data-grid.no-items-found') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            <select wire:model.live="rowsPerPage" class="form-select w-auto">
                @foreach($rowsOptions as $option)
                    <option>{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            {{ $pagination->withQueryString()->links() }}
        </div>
    </div>

    <div wire:loading.delay>
        <div class="w-100 h-100 position-absolute top-0 start-0 bg-light d-flex align-items-center justify-content-center"
             style="--bs-bg-opacity: .7">
            <div class="spinner-border" role="status"></div>
        </div>
    </div>
</div>
