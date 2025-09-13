
@section('css')
    @include('components.table.css')
@endsection

@props([
    'headers' => [],
    'footers' => [],
    'class' => '',
    'idTable' => null,
    'buttons' => [],
    'showSelectAll' => true, // Aktifkan atau nonaktifkan Select All
    'showButtons' => [
        'copy' => true,
        'csv' => true,
        'excel' => true,
        'pdf' => true,
        'print' => true,
    ],
    // 'totalPages' => 0,
])

<div class="card">
    <div class="card-body">
        <div id="table-wrapper" class="tansTack_wrapper dt-bootstrap5">
            <div class="row d-flex justify-content-between align-items-center mb-2">
                <!-- Kolom Kiri -->
                <div class="col-sm-auto d-flex align-items-center gap-2">
                    <span>Show</span>
                    <select class="rows-per-page form-select form-select-sm" id="rows-per-page_{{ $idTable }}"
                        data-table-id="{{ $idTable }}">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>entries</span>
                </div>

                <!-- Kolom Tengah -->
                <div class="col-sm-6 text-center">
                    <div class="dt-buttons btn-group flex-wrap">
                        @foreach ($buttons as $button)
                            {!! $button !!}
                        @endforeach

                        @if ($showButtons['copy'])
                            <button class="btn btn-sm btn-info" id="btnCopy">Copy</button>
                        @endif
                        @if ($showButtons['csv'])
                            <button class="btn btn-sm btn-success" id="btnCSV">CSV</button>
                        @endif
                        @if ($showButtons['excel'])
                            <button class="btn btn-sm btn-warning" id="btnExcel">Excel</button>
                        @endif
                        @if ($showButtons['pdf'])
                            <button class="btn btn-sm bg-teal" id="btnPDF">PDF</button>
                        @endif
                        @if ($showButtons['print'])
                            <button class="btn btn-sm bg-orange" id="btnPrint">Print</button>
                        @endif
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-sm-2">
                    <div id="table-filter" class="tansTack_filter d-flex justify-content-end align-items-center">
                        <span class="me-2">Search:</span>
                        <input type="text" id="searchInput_{{ $idTable }}" class="form-control form-control-sm">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="{{ $idTable }}" class="table table-sm table-striped table-hover table-bordered {{ $class }}">
                    <thead>
                        <tr>
                            @if ($showSelectAll)
                                <th><input type="checkbox" class="all-checkbox" id="{{ $idTable }}_selectAll"></th>
                            @endif
                            @foreach ($headers as $index => $header)
                                <th id="{{ $idTable }}_header-{{ $index }}" class="sortable"
                                    data-column="{{ \Illuminate\Support\Str::snake($header) }}">
                                    {{ $header }}
                                    <i class="fa fa-sort sort-icon" style="color: #cfcfcf;"></i>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="{{ $idTable }}_tbody"></tbody>
                    <tfoot>
                        <tr>
                            @foreach ($footers as $footer)
                                <th>{{ $footer }}</th>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>


            <!-- Paginasi -->
            <div class="row mb-2">
                <div class="col-sm-4 tansTack_info d-flex justify-content-start" id="{{ $idTable }}_info">
                    <!-- Info jumlah data -->
                </div>
                <div class="col-sm-4 d-flex justify-content-between align-items-center">
                    <!-- Bisa ditambahkan info lain -->
                </div>

                <div class="col-sm-4">
                    {{-- @if ($totalPages > 1) --}}
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-end"
                            id="{{ $idTable }}_pagination-list">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous"
                                    id="{{ $idTable }}_previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            {{-- @for ($i = 1; $i <= $totalPages; $i++)
                                    <li class="page-item">
                                        <a class="page-link" href="#"
                                            data-page="{{ $i }}">{{ $i }}</a>
                                    </li>
                                @endfor --}}
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Next" id="{{ $idTable }}_next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    {{-- @endif --}}
                </div>

                <div id="{{ $idTable }}_processing" class="tansTack_processing" role="status">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>
</div>
