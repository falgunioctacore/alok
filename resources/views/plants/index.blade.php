@extends('layouts.app')

@section('title', 'Plants')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    @parent
    <h1 class="font-weight-bold text-primary">
        <i class="fas fa-industry mr-2"></i> Plant Management
    </h1>
     <div class="btn-group">
    <button id="addBtn" class="btn btn-primary shadow-sm px-3">
        <i class="fa fa-plus mr-1"></i> Add Plant
    </button>
    <button id="importBtn" class="btn btn-outline-light shadow-sm px-3 ml-2" data-toggle="modal" data-target="#importModal">
        <i class="fa fa-file-import mr-1"></i> Import
    </button>&nbsp;
       <a href="{{ route('plant.export') }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            </div>
</div>
<hr class="mt-2 mb-4 border-primary">
<div id="alertBox">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
</div>
@stop

@section('content')
<div class="card shadow-sm border-0 rounded-lg">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-2">
        <h5 class="mb-0"><i class="fas fa-industry mr-2"></i> Plant List</h5>
        <div class="btn-group">
         
        </div>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle crud-table" 
                   data-api-url="{{ url('api/plants') }}">
                <thead class="bg-light">
                    <tr class="text-success">
                        <th style="width: 80px;">Sr.No</th>
                        <th  data-field="name">Plant Name</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

        <!-- Import Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form id="importForm" method="POST" action="{{ url('plants/import') }}" enctype="multipart/form-data" class="w-100">
                    @csrf
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="importModalLabel">
                                <i class="fas fa-file-import mr-2"></i> Import Plants
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body py-4">
                            <div id="importErrorBox" class="alert alert-danger d-none"></div>
                            <div class="form-group">
                                <label class="font-weight-semibold">Select file (CSV / XLSX)</label>
                                <input type="file" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="form-control-file" required>
                            </div>
                            <p class="small text-muted">Tip: file should contain a column named <strong>name</strong>.</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Don't have a template?</small>
                                <a href="{{ url('plant/template/download') }}" class="btn btn-sm btn-outline-secondary ml-2">
                                    <i class="fa fa-download mr-1"></i> Download Template
                                </a>
                            </div>
                        </div>
                        <div class="modal-footer border-0 d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-secondary px-3" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-info px-3">
                                <i class="fas fa-file-import mr-1"></i> Import
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

<!-- Modal -->
<div class="modal fade" id="crudModal" tabindex="-1" role="dialog" aria-labelledby="crudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="crudForm" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="crudModalLabels">
                        <i class="fas fa-pen-to-square mr-2"></i> Add / Edit Plant
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">
                    <div id="errorBox" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label class="font-weight-semibold">🌱 Plant Name</label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="Enter plant name" required>
                    </div>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-secondary px-3" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success px-3">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@push('css')
<link rel="stylesheet" link="{{ asset('css/index.css') }}">
@endpush

@push('js')
<script src="{{ asset('js/crud.js') }}"></script>
@endpush
