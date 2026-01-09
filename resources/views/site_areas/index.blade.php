@extends('layouts.app')

@section('title', 'Site Areas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    @parent
    <h1 class="font-weight-bold text-primary">
        <i class="fas fa-map-marked-alt mr-2"></i> Site Area Management
    </h1>
    <button id="addBtn" class="btn btn-primary shadow-sm px-3">
        <i class="fa fa-plus mr-1"></i> Add Site Area
    </button>
</div>
<hr class="mt-2 mb-4 border-info">
<div id="alertBox"></div>
@stop

@section('content')
<div class="card shadow-sm border-0 rounded-lg">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-2">
        <h5 class="mb-0"><i class="fas fa-map mr-2"></i> Site Area List</h5>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle crud-table" 
                   data-api-url="{{ url('api/site-areas') }}">
                <thead class="bg-light">
                    <tr class="text-info">
                        <th style="width: 80px;">Sr.No</th>
                        <th data-field="name">Site Area Name</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="crudModal" tabindex="-1" role="dialog" aria-labelledby="crudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="crudForm" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="crudModalLabel">
                        <i class="fas fa-pen-to-square mr-2"></i> Add / Edit Site Area
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">
                    <div id="errorBox" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label class="font-weight-semibold">📍 Site Area Name</label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="Enter site area name" required>
                    </div>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-secondary px-3" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-info px-3">
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
