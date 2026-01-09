@extends('layouts.app')
@section('title', 'Attendance')

@section('content_header')
<h1>Employee Attendance</h1>
@parent
@stop

@section('content')
<div class="card">
    <div class="card-body text-center">
        <div class="form-group">
            <label>Select Employee</label>
            <select id="employeeSelect" class="form-control w-50 mx-auto">
                <option value="">-- Select Employee --</option>
                @foreach(\App\Models\Employee::all() as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->emp_name }}</option>
                @endforeach
            </select>
        </div>

        <button id="markInBtn" class="btn btn-success btn-lg m-2">
            <i class="fa fa-sign-in-alt"></i> Mark IN
        </button>

        <button id="markOutBtn" class="btn btn-danger btn-lg m-2">
            <i class="fa fa-sign-out-alt"></i> Mark OUT
        </button>

        <div id="reasonContainer" class="mt-3" style="display:none;">
            <label>Reason for OUT</label>
            <select id="reasonSelect" class="form-control w-50 mx-auto">
                <option value="">Select Reason</option>
                <option>Other Plant</option>
                <option>Lunch / Dinner</option>
                <option>Early Leave / OFF Duty</option>
                <option>OD</option>
                <option>Alok City</option>
                <option>Gate Pass</option>
            </select>
        </div>
    </div>
</div>
<div id="alertBox"></div>
<div class="card mt-4">
    <div class="card-header"><strong>Today's Attendance Records</strong></div>
    <div class="card-body">
        <table class="table table-bordered text-center" id="attendanceTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>In Geo Fencing Point</th>
                    <th>IN Time</th>
                    <th>Out Geo Fencing Point</th>
                    <th>OUT Time</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

<script>
const apiUrl = "{{ url('/api/attendances') }}";

async function fetchAttendance() {
    const res = await fetch(apiUrl);
    const data = await res.json();
    const tbody = document.querySelector("#attendanceTable tbody");
    tbody.innerHTML = "";
    data.forEach((row, i) => {
        tbody.innerHTML += `
            <tr>
                <td>${i + 1}</td>
                <td>${row.employee?.emp_name || '-'}</td>
                <td>${row.in_geo_fencing_point?.location || '-'}</td>
                <td>${row.in_time ? new Date(row.in_time).toLocaleString() : '-'}</td>
                <td>${row.out_geo_fencing_point?.location || '-'}</td>
                <td>${row.out_time ? new Date(row.out_time).toLocaleString() : '-'}</td>
                <td>${row.reason || '-'}</td>
            </tr>
        `;
    });
}

async function markAttendance(type, reason = '') {
    const employee_id = document.getElementById('employeeSelect').value;
    if (!employee_id) return toastr.error("Please select an employee first.");

    if (type === 'out' && !reason) return toastr.warning("Please select reason for OUT");

    if (!navigator.geolocation) {
        toastr.error("Geolocation not supported");
        return;
    }

    navigator.geolocation.getCurrentPosition(async (pos) => {
        const latitude = pos.coords.latitude;
        const longitude = pos.coords.longitude;

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ employee_id, latitude, longitude, type, reason })
            });

            const result = await response.json();
            if (response.ok) {
                toastr.success(result.message || `${type.toUpperCase()} recorded successfully!`);
                fetchAttendance();
                document.getElementById('reasonContainer').style.display = 'none';
                document.getElementById('reasonSelect').value = '';
            } else {
                toastr.error(result.message || "Something went wrong");
            }
        } catch (error) {
            console.error(error);
            toastr.error("Network or server error");
        }
    }, () => toastr.error("Please allow location access."));
}

// ✅ Attach button events
document.getElementById('markInBtn').addEventListener('click', () => {
    if (confirm("Confirm Mark IN?")) {
        markAttendance('in');
    }
});

document.getElementById('markOutBtn').addEventListener('click', () => {
    // Step 1: show reason dropdown
    const reasonContainer = document.getElementById('reasonContainer');
    reasonContainer.style.display = 'block';

    // Step 2: When user selects a reason → submit OUT
    const reasonSelect = document.getElementById('reasonSelect');
    reasonSelect.addEventListener('change', function handler() {
        if (this.value) {
            if (confirm("Confirm Mark OUT?")) {
                markAttendance('out', this.value);
                reasonSelect.removeEventListener('change', handler); // prevent double calls
            }
        }
    });
});

fetchAttendance();
</script>
@stop
