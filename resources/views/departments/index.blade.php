@extends('layouts.app')

@section('title', 'Departments')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-3">
    @parent
    <h1 class="fw-bold text-primary mb-0">Department Master</h1>
    <div class="btn-group">
        <button id="addBtn" class="btn btn-primary btn-sm shadow-sm">
            <i class="fa fa-plus me-1"></i> Add Department
        </button>
        <button id="importBtn" class="btn btn-secondary  btn-sm shadow-sm ml-2" data-toggle="modal"
            data-target="#importModal">
            <i class="fas fa-file-import me-1"></i> Import
        </button>
        <a href="{{ route('department.export') }}" class="btn btn-success btn-sm shadow-sm ml-2">
            <i class="fas fa-file-excel me-1"></i> Export Excel
        </a>
    </div>
</div>
<div id="alertBox"></div>
@stop
@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h3 class="card-title fw-semibold text-dark mb-0">
            <i class="fa fa-sitemap text-primary me-1"></i> Department List
        </h3>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-hover table-striped align-middle crud-table"
            data-api-url="{{ url('api/departments') }}">
            <thead class="bg-primary text-white">
                <tr>
                    <th class="text-center" width="50">#</th>
                    <th data-field="name">Department Name</th>
                    <th width="120" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-center"></tbody>
        </table>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="importForm" method="POST" action="{{ route('departments.import') }}" enctype="multipart/form-data"
            class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="fas fa-file-import mr-2"></i> Import Departments
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">
                    <div id="importErrorBox" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label class="font-weight-semibold">Select file (CSV / XLSX)</label>
                        <input type="file" name="file"
                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                            class="form-control-file" required>
                    </div>
                    <p class="small text-muted">Tip: file should contain a column named <strong>name</strong>.</p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted">Don't have a template?</small>
                        <a href="{{ route('department.template') }}" class="btn btn-sm btn-outline-secondary ml-2">
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
                        <i class="fas fa-pen-to-square mr-2"></i> Add / Edit Department
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">
                    <div id="errorBox" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label class="font-weight-semibold">Department Name</label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="Enter plant name"
                            required>
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
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

@push('js')
    <script src="{{ asset('js/crud.js') }}"></script>
@endpush