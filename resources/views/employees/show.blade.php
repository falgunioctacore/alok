@extends('layouts.app')

@section('title', 'Employee Details')

@section('content_header')
    <h1>Employee Details</h1>
     @parent
@stop

@section('content')

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h4 id="titleBar" class="mb-0">Loading...</h4>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">
                <h5 class="text-primary font-weight-bold mb-3">Employee Information</h5>

                <p><strong>Name:</strong> <span id="name"></span></p>
                <p><strong>Employee Code:</strong> <span id="code"></span></p>
                <p><strong>Mobile:</strong> <span id="mobile"></span></p>
                <p><strong>Email:</strong> <span id="email"></span></p>
                <p><strong>Plant:</strong> <span id="plant"></span></p>
            </div>

            <div class="col-md-6 text-center">
                <h5 class="text-primary font-weight-bold mb-3">QR Code</h5>

                <img id="qrImage" class="img-thumbnail d-none" style="max-width: 250px;">

                <div id="qrButtons" class="mt-3 d-none">
                    <a id="downloadQr" class="btn btn-success btn-sm">
                        <i class="fa fa-download"></i> Download QR
                    </a>

                    <button id="generateQr" class="btn btn-info btn-sm">
                        <i class="fa fa-refresh"></i> Regenerate QR
                    </button>
                </div>

                <p id="qrMissing" class="text-danger font-weight-bold d-none">
                    QR Code not generated
                </p>
            </div>

        </div>

    </div>
</div>

@endsection

@push('js')
<script>
    const employeeId = "{{ $id }}";

    function loadEmployee() {
        fetch(`/api/employees/${employeeId}/e`)
            .then(res => res.json())
            .then(res => {
                let emp = res.employee;
                document.getElementById('titleBar').innerText =
                    `${emp.emp_name??'--'} (${emp.emp_code??'--'})`;

                document.getElementById('name').innerText = emp.emp_name??'--';
                document.getElementById('code').innerText = emp.emp_code;
                document.getElementById('mobile').innerText = emp.emp_mobile_no;
                document.getElementById('email').innerText = emp.emp_email_id ?? '--';
                document.getElementById('plant').innerText = emp.plant?.name ?? '--';

                if (res.qr_url) {
                    document.getElementById('qrImage').src = res.qr_url;
                    document.getElementById('qrImage').classList.remove('d-none');
                    document.getElementById('qrButtons').classList.remove('d-none');
                    document.getElementById('downloadQr').href = `/api/employees/${employeeId}/download-qr`;
                } else {
                    document.getElementById('qrMissing').classList.remove('d-none');
                }
            });
    }

    document.getElementById('generateQr').addEventListener('click', function () {
        fetch(`/api/employees/${employeeId}/generate-qr`, { method: 'POST' })
            .then(res => res.json())
            .then(res => {
                loadEmployee();
            });
    });

    loadEmployee();
</script>
@endpush
