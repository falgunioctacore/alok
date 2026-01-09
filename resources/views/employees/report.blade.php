@php
    $heads = [
        'SR NO',
        'Employee Name',
        'Employee Code',
        'In or OUT',
        'In/Out Date Time',
        'Gate',
        'Reason'
    ];
@endphp

@extends('layouts.app')

@section('title', 'Employee Attendance Report')

@section('content_header')
    <h1>Employee Attendance Report</h1>
@stop

@section('content')


<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Attendance Report</h3>
    </div>

    <div class="card-body">

        <!-- ===========================
             FILTER FORM (API BASED)
        ============================ -->
        <form id="filterForm" class="mb-4" onsubmit="event.preventDefault(); loadReport();">
            <div class="row g-4">

                <!-- EXPORT BUTTONS -->
                <div class="col-md-12 mb-3">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-success btn-sm mx-2" id="exportExcelBtn"><i class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-info btn-sm mx-2" id="exportCsvBtn"><i class="fa fa-file-csv"></i> CSV</button>
                        <button class="btn btn-danger btn-sm mx-2" id="exportPdfBtn"><i class="fa fa-file-pdf"></i> PDF</button>
                    </div>
                </div>

                <!-- EMPLOYEE -->
                <div class="col-md-3">
                    <label class="font-weight-bold">Employees</label>
                    <select id="employees" class="form-control select2" multiple>
                        <option value="">Select Employee</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->emp_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- PLANTS -->
                <div class="col-md-3">
                    <label class="font-weight-bold">Plant</label>
                    <select id="plant_id" class="form-control select2" multiple>
                        @foreach ($plants as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- IN GATE -->
               {{-- <div class="col-md-3">
                    <label class="font-weight-bold">IN Gate</label>
                    <select id="in_gate" class="form-control select2" multiple>
                        @foreach ($geoFencingPoints as $g)
                            <option value="{{ $g->id }}">{{ $g->location }}</option>
                        @endforeach
                    </select>
                </div>--}}
                <div class="col-md-3">
                    <label class="font-weight-bold">Gates</label>
                    <select  id="gates" class="form-control select2" name="gates[]" multiple>
                        @foreach ($geoFencingPoints as $g)
                            <option value="{{ $g->id }}">{{ $g->location }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- OUT GATE -->
               {{-- <div class="col-md-3">
                    <label class="font-weight-bold">OUT Gate</label>
                    <select id="out_gate" class="form-control select2" multiple>
                        @foreach ($geoFencingPoints as $g)
                            <option value="{{ $g->id }}">{{ $g->location }}</option>
                        @endforeach
                    </select>
                </div>--}}

                <!-- DATE FILTER -->
                <div class="col-md-3">
                    <label class="font-weight-bold">Date Range</label>
                    <select id="date_filter" class="form-control select2">
                        <option value="">Any</option>
                        <option value="today">Today</option>
                        <option value="tomorrow">Tomorrow</option>
                        <option value="weekly">This Week</option>
                        <option value="monthly">This Month</option>
                        <option value="yearly">This Year</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                <!-- CUSTOM DATE -->
                <div class="col-md-3 custom-date-range d-none">
                    <label>From</label>
                    <input type="date" id="from_date" class="form-control">

                    <label>To</label>
                    <input type="date" id="to_date" class="form-control">
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary w-100 mt-4">Apply Filters</button>
                </div>
            </div>
        </form>

        <!-- ===========================
             DATA TABLE (DYNAMIC API)
        ============================ -->
        <x-adminlte-datatable id="employeeTable" :heads="$heads" striped hoverable bordered>
            <tbody id="reportBody"></tbody>
        </x-adminlte-datatable>
    </div>
</div>
@stop

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>
$('.select2').select2();

// CUSTOM DATE SHOW / HIDE
$('#date_filter').on('change', function () {
    if ($(this).val() === 'custom') {
        $('.custom-date-range').removeClass('d-none');
    } else {
        $('.custom-date-range').addClass('d-none');
    }
});

// ===============================
//    API DATA LOAD FUNCTION
// ===============================
function loadReport() {
    let params = {
        employee_id: $('#employees').val(),
        plant_id: $('#plant_id').val(),
        // in_gate_id: $('#in_gate').val(),
        // out_gate_id: $('#out_gate').val(),
         gate_id: $('#gates').val(),
        date_type: $('#date_filter').val(),
        from_date: $('#from_date').val(),
        to_date: $('#to_date').val(),
    };

    fetch("{{ url('/api/employee/attendence/report') }}?" + new URLSearchParams(params))
        .then(res => res.json())
        .then(data => renderTable(data));
}

// ===============================
//     RENDER TABLE (DYNAMIC)
// ===============================
function renderTable(data) {
    let tbody = document.getElementById("reportBody");
    tbody.innerHTML = "";

    data.forEach((row, i) => {
        tbody.innerHTML += `
            <tr>
                <td>${i + 1}</td>
                <td>${row.employee.emp_name??'---'}</td>
                <td>${row.employee.emp_code??'---'}</td>
                <td>${row.type??'---'}</td>
                <td>${row.attendance_date ?? '---'}</td>
                <td>${row.geo_fencing_point?.location ?? '---'}</td>
                <td>${row.reason ?? '---'}</td>
            </tr>
        `;
    });
}

// ===============================
//        EXPORT — EXCEL
// ===============================
document.getElementById('exportExcelBtn').addEventListener('click', function (event) {
    event.preventDefault();
    var wb = XLSX.utils.table_to_book(document.getElementById('employeeTable'));
    XLSX.writeFile(wb, 'attendance_report.xlsx');
});

// ===============================
//        EXPORT — CSV
// ===============================
document.getElementById('exportCsvBtn').addEventListener('click', function (event) {
    event.preventDefault();
    var table = document.getElementById('employeeTable');
    var rows = [...table.rows].map(r => [...r.cells].map(c => c.innerText));
    var csv = Papa.unparse(rows);
    var blob = new Blob([csv], { type: 'text/csv' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'attendance_report.csv';
    link.click();
});

// ===============================
//        EXPORT — PDF
// ===============================
document.getElementById('exportPdfBtn').addEventListener('click', function (event) {
    event.preventDefault();

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    let table = document.querySelector('#employeeTable');
    let headers = [...table.rows[0].cells].map(h => h.innerText);
    let data = [...table.rows].slice(1).map(r =>
        [...r.cells].map(c => c.innerText)
    );

    doc.text("Employee Attendance Report", 14, 10);
    doc.autoTable({ head: [headers], body: data, startY: 20 });
    doc.save('attendance_report.pdf');
});
</script>
@endpush
