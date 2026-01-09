@extends('layouts.app')

@section('title', 'Vehicle Details')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    @parent
    <h1 class="font-weight-bold text-primary">
        <i class="fas fa-truck-moving mr-2"></i> Vehicle Master
    </h1>
    <div>
        <button id="importBtn" class="btn btn-success shadow-sm px-3 mr-2">
            <i class="fa fa-file-excel mr-1"></i> Import Excel
        </button>
        <button id="addBtn" class="btn btn-primary shadow-sm px-3">
            <i class="fa fa-plus mr-1"></i> Add Vehicle
        </button>
    </div>
</div>
<hr class="mt-2 mb-4 border-primary">

@stop

@section('content')

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h4 id="titleBar" class="mb-0">Loading...</h4>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">
                <h5 class="text-primary font-weight-bold mb-3">Vehicle Information</h5>

                <p><strong>Name:</strong> <span id="name"></span></p>
                <p><strong>Employee Code:</strong> <span id="code"></span></p>
                <p><strong>Vehicle No:</strong> <span id="Vehicle_no"></span></p>
                <p><strong>Pass No.:</strong> <span id="passno"></span></p>
                <p><strong>Mobile No.:</strong> <span id="mobileNo"></span></p>
                <p><strong>Email:</strong> <span id="email_id"></span></p>
                <p><strong>Driving License No:</strong> <span id="license_no"></span></p>
                <p><strong>Driving License Validity:</strong> <span id="license_validity"></span></p>
                <p><strong>RC Validity:</strong> <span id="rc_validity"></span></p>
                <p><strong>PUC Validity:</strong> <span id="puc_validity"></span></p>
                <p><strong>Insurance Validity:</strong> <span id="ins_validity"></span></p>
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
    const VehicleId = "{{ $id }}";

    function loadVehicle() {
        console.log(VehicleId);
        fetch(`/api/vehicles/${VehicleId}/e`)
            .then(res => res.json())
            .then(res => {
                let emp = res.vehicle;
    
                document.getElementById('titleBar').innerText =
                    `${emp.name??'--'} (${emp.emp_code??'--'})`;
                      let passno = emp.pass_no;
                      let formattedPassNo = 'ASVP' + ('0000' + passno).slice(-4)

                document.getElementById('name').innerText = emp.name??'--';
                document.getElementById('code').innerText = emp.emp_code;
                document.getElementById('passno').innerText = formattedPassNo??'--';
                document.getElementById('Vehicle_no').innerText = emp.vehicle_no;
                document.getElementById('mobileNo').innerText = emp.contact_no??'--';
                document.getElementById('email_id').innerText = emp.email_id??'--';
                document.getElementById('license_no').innerText = emp.driving_license_no ?? '--';
                document.getElementById('license_validity').innerText = emp.driving_license_validity ?? '--';
                document.getElementById('rc_validity').innerText = emp.rc_validity ?? '--';
                document.getElementById('puc_validity').innerText = emp.puc_validity ?? '--';
                document.getElementById('ins_validity').innerText = emp.insurance_validity ?? '--';

                if (res.qr_url) {
                    document.getElementById('qrImage').src = res.qr_url;
                    document.getElementById('qrImage').classList.remove('d-none');
                    document.getElementById('qrButtons').classList.remove('d-none');
                    document.getElementById('downloadQr').href = `/api/Vehicles/${VehicleId}/download-qr`;
                } else {
                    document.getElementById('qrMissing').classList.remove('d-none');
                }
            });
    }

    document.getElementById('generateQr').addEventListener('click', function () {
        fetch(`/api/vehicles/${VehicleId}/generate-qr`, { method: 'POST' })
            .then(res => res.json())
            .then(res => {
                loadVehicle();
            });
    });

    loadVehicle();
</script>
@endpush
