@extends('layouts.app')

@section('title', 'Geo Fencing Point')

@section('content_header')
 <div class="d-flex justify-content-between align-items-center">
    @parent
    <h1 class="font-weight-bold text-primary">
         Geo Fencing Point
    </h1>
    <button id="addBtn" class="btn btn-primary btn-md">
            <i class="fa fa-plus"></i> Add Geo Fencing Point
    </button>
</div>
<div id="alertBox"></div>
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Geo Fencing Point List</h3>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped crud-table" data-api-url="{{ url('/api/access-points') }}">
            <thead>
                <tr>
                    <th>#</th>
                    <th data-field="plant">Plant</th>
                    <th data-field="gate">Gate No</th>
                    <th data-field="location">Location</th>
                    <th data-field="latitude">Latitude</th>
                    <th data-field="longitude">Longitude</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="crudForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crudModalLabel">Add Geo Access Point</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Plant</label>
                        <select name="plant_id" id="plant_id" class="form-control">
                            <option value="">Select Plant</option>
                            @foreach(\App\Models\Plant::all() as $plant)
                                <option value="{{ $plant->id }}">{{ $plant->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Name</label>
                        <input type="text" name="location" id="location" class="form-control" required>
                    </div>
                   <div class="col-md-6 form-group">
                        <label>Gate Count</label>
                        <input type="number" name="gate_count" id="gate" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Latitude</label>
                        <input type="text" name="latitude" id="latitude" class="form-control">
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Longitude</label>
                        <input type="text" name="longitude" id="longitude" class="form-control">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@stop
@push('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}" >
@endpush

@section('js')
<script src="{{ asset('js/crud.js') }}"></script>
@stop
