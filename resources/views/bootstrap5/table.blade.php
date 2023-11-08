<div class="datatable container-fluid">
    {{ $template }}
    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <colgroup>
                @foreach($source['columns'] as $column)
                    <col style="width: {{ $column['width'] }}"/>
                @endforeach
            </colgroup>
            <thead>
            <tr>
                <td colspan="{{ count($source['columns']) }}" class="text-end">
                    @if($source['actions'])
                        {!! $source['actions']() !!}
                    @endif

                    @if($source['hasFilters'])
                        <button class="btn btn-secondary" wire:click="clearFilters" type="button">
                            {{ __('data-grid::data-grid.actions.clear-filters') }}
                        </button>
                    @endif

                    <button class="btn btn-secondary" wire:click="clearSorting" type="button">
                        {{ __('data-grid::data-grid.actions.clear-sorting') }}
                    </button>
                </td>
            </tr>
            @if($source['hasFilters'])
                <tr style="vertical-align: top">
                    @foreach($source['columns'] as $column)
                        <td>
                            @if($column['filter'] && $column['from'])
                                {!! [$filterClasses[$column['filter']], 'template']('filter.'.$column['from']) !!}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endif
            <tr>
                @foreach($source['columns'] as $column)
                    <th>
                        <div class="row m-0">
                            <div class="col px-0">
                                <span class="text-truncate mb-2">{{ $column['label'] }}</span>
                            </div>
                            <div class="col-auto px-0">
                                @if ($column['from'] && $column['sorting'])
                                    <div class="dropdown d-inline-block">
                                        <a class="btn btn-sm btn-secondary dropdown-toggle"
                                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-sort-down"></i>
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
                            </div>
                        </div>
                    </th>
                @endforeach
            </tr>
            </thead>

            <tbody>
            @forelse($pagination as $row)
                <tr>
                    @foreach($source['columns'] as $column)
                        @empty($column['renderer'])
                            <td>{{ $row->{$column['from']} }}</td>
                        @else
                            <td>{!! $column['renderer']($row->toArray()) !!}</td>
                        @endempty
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhum item foi encontrado</td>
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
</div>
