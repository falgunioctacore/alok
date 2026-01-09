@extends('layouts.app')

@section('title', 'Vehicle Master')

@section('content_header')
<style>
    .page-header-bar {
        background: #ffffff;
        padding: 12px 18px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        border-left: 4px solid #007bff;
    }
    .page-title-icon {
        font-size: 28px;
        color: #007bff;
        margin-right: 10px;
    }
    .action-buttons .btn {
        margin-left: 8px;
    }
</style>

<div class="page-header-bar d-flex justify-content-between align-items-center">

    {{-- LEFT SIDE: Back + Title --}}
    <div class="d-flex align-items-center">
        @parent

        <h3 class="m-0 text-primary d-flex align-items-center">
            <i class="fas fa-truck-moving page-title-icon"></i>
            Vehicle Master
        </h3>
    </div>

    {{-- RIGHT SIDE ACTION BUTTONS --}}
    <div class="action-buttons d-flex">

        <button id="importBtn" class="btn btn-success btn-sm shadow-sm">
            <i class="fa fa-file-excel mr-1"></i> Import Excel
        </button>

        <button id="addBtn" class="btn btn-primary btn-sm shadow-sm">
            <i class="fa fa-plus mr-1"></i> Add Vehicle
        </button>

        <a href="{{ url('/api/vehicles/download-all-qrcodes')}}" 
           class="btn btn-danger btn-sm shadow-sm"
           target="_blank">
            <i class="fas fa-qrcode mr-1"></i> QR Codes
        </a>

        <button id="exportExcel" class="btn btn-success btn-sm shadow-sm">
            <i class="fas fa-file-excel mr-1"></i> Export Excel
        </button>

        <button id="exportPDF" class="btn btn-danger btn-sm shadow-sm">
            <i class="fas fa-file-pdf mr-1"></i> Export PDF
        </button>

    </div>
</div>

<hr class="mt-3 mb-3">
<div id="alertBox"></div>
<x-alert-component class="my-2" />
@stop




@section('content')
<div class="card shadow-sm border-0 rounded-lg">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-2">
        <h5 class="mb-0"><i class="fas fa-car-side mr-2"></i> Vehicle List</h5>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle crud-table"
                   data-api-url="{{ url('/api/vehicles') }}">
                <thead class="bg-light">
                    <tr class="text-primary">
                        <th width="50">#</th>
                        <th data-field="vehicle_no">Vehicle No</th>
                        <th data-field="vehicle_type">Vehicle Type</th>
                        <th data-field="emp_code">Employee Code</th>
                        <th data-field="pass_no">Pass No</th>
                        <th data-field="name">Name</th>
                        <th data-field="email_id">Email</th>
                        <th data-field="contact_no">contact_no</th>
                        <th data-field="driving_license_no">Driving License No</th>
                        <th data-field="driving_license_validity">Driving License Validity</th>
                        <th data-field="rc_validity">RC Validity</th>
                        <th data-field="puc_validity">PUC Validity</th>
                        <th data-field="insurance_validity">Insurance Validity</th>
                        <th data-field="residence">Residence</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="crudForm" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="crudModalLabel">
                    <i class="fas fa-car mr-2"></i> Add / Edit Vehicle
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body py-4">
                <div id="errorBox" class="alert alert-danger d-none"></div>
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>🚘 Vehicle No</label>
                        <input type="text" name="vehicle_no" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>🚗 Vehicle Type</label>
                        <input type="text" name="vehicle_type" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>👷 Employee Code</label>
                        <input type="text" name="emp_code" class="form-control shadow-sm" required>
                    </div>
                  {{--  <div class="col-md-4 form-group">
                        <label>👷 Pass No</label>
                        <input type="text" name="pass_no" class="form-control shadow-sm" required>
                    </div> --}}
                    <div class="col-md-4 form-group">
                        <label>🧍 Name</label>
                        <input type="text" name="name" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>📧 Email</label>
                        <input type="email" name="email_id" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>🪪 Driving License No</label>
                        <input type="text" name="driving_license_no" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>📅 Driving License Validity</label>
                        <input type="date" name="driving_license_validity" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>📄 RC Validity</label>
                        <input type="date" name="rc_validity" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>🌫️ PUC Validity</label>
                        <input type="date" name="puc_validity" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>🛡️ Insurance Validity</label>
                        <input type="date" name="insurance_validity" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>📞 Contact No</label>
                        <input type="text" name="contact_no" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>🏠 Residence</label>
                        <input type="text" name="residence" class="form-control shadow-sm" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-end">
                <button type="button" class="btn btn-outline-secondary px-3" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary px-3">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Import Excel Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form action="{{ route('vehicles.import') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-excel mr-2"></i> Import Vehicle Data
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="text-muted mb-3">Please upload a valid Excel file (.xlsx or .csv)</p>
                <input type="file" name="file" class="form-control-file mb-3" required accept=".xlsx,.csv">
                <a href="{{ route('vehicle.template') }}" class="text-primary">
                    <i class="fa fa-download mr-1"></i> Download Sample Format
                </a>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-end">
                <button type="button" class="btn btn-outline-secondary px-3" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-success px-3">
                    <i class="fas fa-upload mr-1"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@push('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

@section('js')
<script src="{{ asset('js/crud.js') }}"></script>
<script>
document.getElementById('importBtn').addEventListener('click', function() {
    $('#importModal').modal('show');
});
</script>
@stop
