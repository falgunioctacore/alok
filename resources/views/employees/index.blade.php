@extends('layouts.app')

@section('title', 'Permitted Employees')

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
            <i class="fas fa-id-card-alt page-title-icon"></i>
            Permitted Employee Master
        </h3>
    </div>

    {{-- RIGHT SIDE ACTION BUTTONS --}}
    <div class="action-buttons d-flex">

        <button id="addBtn" class="btn btn-primary btn-sm shadow-sm">
            <i class="fa fa-plus mr-1"></i> Add Employee
        </button>

        <button id="importBtn" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-file-import mr-1"></i> Import
        </button>

        <a href="{{ url('api/employees/download-all-qrcodes') }}"
           class="btn btn-success btn-sm shadow-sm"
           target="_blank">
            <i class="fas fa-qrcode mr-1"></i> QR Codes
        </a>

        <button id="exportExcel" class="btn btn-success btn-sm shadow-sm">
            <i class="fas fa-file-excel mr-1"></i> Excel
        </button>

        <button id="exportPDF" class="btn btn-danger btn-sm shadow-sm">
            <i class="fas fa-file-pdf mr-1"></i> PDF
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
        <h5 class="mb-0"><i class="fas fa-users mr-2"></i> Employee List</h5>
    </div>

    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle crud-table"
                   data-api-url="{{ url('/api/employees') }}">
                <thead class="bg-light">
                    <tr class="text-primary">
                        <th>#</th>
                        <th data-field="emp_name">Name</th>
                        <th data-field="emp_code">Employee Code</th>
                        <th data-field="employee_id">Employee Id</th>
                        <th data-field="emp_email_id">Email</th>
                        <th data-field="site_area">Site</th>
                        <th data-field="plant">Plant</th>
                        <th data-field="department">Department</th>
                        <th data-field="emp_mobile_no">Mobile No</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="crudForm" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="crudModalLabel">
                    <i class="fas fa-user-plus mr-2"></i> Add / Edit Employee
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body py-4">
                <div id="errorBox" class="alert alert-danger d-none"></div>
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>👤 Name</label>
                        <input type="text" name="emp_name" class="form-control shadow-sm" placeholder="Enter employee name" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>🧾 Employee Code</label>
                        <input type="text" name="emp_code" class="form-control shadow-sm" placeholder="Employee code" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>📧 Email</label>
                        <input type="email" name="emp_email_id" class="form-control shadow-sm" placeholder="Email address">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>📞 Mobile No</label>
                        <input type="text" name="emp_mobile_no" class="form-control shadow-sm" placeholder="Mobile number">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>🏗️ Site Area</label>
                        <select name="site_area_id" class="form-control shadow-sm" id="site_area_id">
                            <option value="">Select Site Area</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>🏭 Plant</label>
                        <select name="plant_id" class="form-control shadow-sm" id="plant_id">
                            <option value="">Select Plant</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>🏢 Department</label>
                        <select name="department_id" class="form-control shadow-sm" id="department_id">
                            <option value="">Select Department</option>
                        </select>
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import mr-2"></i> Import Employees
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            {{--<div class="modal-body text-center py-4">
                <p class="text-muted mb-3">Please upload a valid Excel file (.xlsx or .csv)</p>
                <input type="file" name="file" id="importFile" class="form-control-file mb-3" required accept=".xlsx,.csv">
                <a href="{{ asset('samples/vehicle_import_sample.xlsx') }}" class="text-primary">
                    <i class="fa fa-download mr-1"></i> Download Sample Format
                </a>
            </div>--}}
            <div class="modal-body py-4">
                @csrf
              <div class="modal-body text-center py-4">
                  <p class="text-muted mb-3">Please upload a valid Excel file (.xlsx or .csv)</p>
                  <input type="file" name="file" id="importFile" class="form-control-file mb-3" required accept=".xlsx,.csv">
                  <a href="{{ route('employee.template')}}" class="text-primary">
                      <i class="fa fa-download mr-1"></i> Download Sample Format
                  </a>
              </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-end">
                <button type="button" class="btn btn-outline-secondary px-3" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary px-3">
                    <i class="fas fa-upload mr-1"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@push('css')
<link rel="stylesheet" link="{{ asset('css/index.css') }}">
@endpush

@section('js')
<script src="{{ asset('js/crud.js') }}"></script>

<script>
    document.getElementById('importBtn').addEventListener('click', () => {
       $('#importModal').modal('show');
   });

/**
 * Fetch and populate dropdowns for Plants, Site Areas, Departments
 */
document.addEventListener('DOMContentLoaded', async () => {
    await populateDropdown('{{ url("/api/plants") }}', '#plant_id', 'id', 'name');
    await populateDropdown('{{ url("/api/site-areas") }}', '#site_area_id', 'id', 'name');
    await populateDropdown('{{ url("/api/departments") }}', '#department_id', 'id', 'name');
});

async function populateDropdown(apiUrl, selector, valueKey = 'id', labelKey = 'name') {
    try {
        const res = await fetch(apiUrl);
        const data = await res.json();
        const select = document.querySelector(selector);
        if (!select) return;
        select.innerHTML = '<option value="">Select</option>';
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[valueKey];
            opt.textContent = item[labelKey];
            select.appendChild(opt);
        });
    } catch (err) {
        console.error('Error loading dropdown:', apiUrl, err);
    }
}
</script>
@stop
