@extends('layouts.app')

@section('title', 'Recycle Bin')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    @parent
    <h1 class="font-weight-bold text-primary">
        <i class="fas fa-id-card-alt mr-2"></i> Recycle Bin
    </h1>
</div>
@stop

@section('content')

<style>
    .trash-tabs .nav-link {
        border-radius: 30px;
        padding: 10px 20px;
        font-weight: 600;
        color: #555 !important;
    }

    .trash-tabs .nav-link.active {
        background: #007bff !important;
        color: #fff !important;
        box-shadow: 0px 4px 8px rgba(0, 123, 255, .4);
    }

    .trash-card {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0px 4px 12px rgba(0,0,0,0.05);
    }

    .fade-out {
        transition: opacity .6s ease-out, transform .3s ease;
        opacity: 0;
        transform: translateX(-10px);
    }

    /* New Bulk Action Buttons UI */
    .bulk-actions {
        padding: 14px 0;
        display: flex;
        gap: 10px;
    }

    .btn-modern {
        padding: 8px 18px;
        font-size: 14px;
        border-radius: 25px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        transition: .2s ease-in-out;
    }

    .btn-restore {
        background: linear-gradient(45deg, #1dd1a1, #10ac84);
        color: white;
    }

    .btn-restore:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 172, 132, .4);
    }

    .btn-delete {
        background: linear-gradient(45deg, #ff6b6b, #ee5253);
        color: white;
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(238, 82, 83, .4);
    }
</style>



@php
$tabs = [
    'Plant' => 'Building, Factory Plants',
    'Department' => 'Departments List',
    'SiteArea' => 'Site Working Areas',
    'Employee' => 'Employee Records',
    'Vehicle' => 'Vehicle Records',
    'GeoFencingPoint' => 'Geo-Fencing Locations',
];
@endphp

<!-- Tabs -->
<ul class="nav nav-pills mb-4 trash-tabs" id="myTab" role="tablist">
    @foreach($tabs as $key => $label)
        <li class="nav-item">
            <a class="nav-link @if ($loop->first) active @endif" 
               id="{{ $key }}-tab" data-toggle="tab" href="#{{ $key }}" role="tab">
               <i class="fas fa-folder-open"></i> {{ $key }}
               <span class="badge badge-primary ml-2">{{ count($models[$key]) }}</span>
            </a>
        </li>
    @endforeach
</ul>



<div class="tab-content">

@foreach($models as $title => $rows)
<div class="tab-pane fade show @if($loop->first) active @endif" id="{{ $title }}" role="tabpanel">

    <!-- Beautiful Bulk Action Buttons -->
    <div class="bulk-actions">
        <button class="btn-modern btn-restore" onclick="bulkRestore('{{ $title }}')">
            <i class="fas fa-undo"></i> Restore Selected
        </button>

        <button class="btn-modern btn-delete" onclick="bulkDelete('{{ $title }}')">
            <i class="fas fa-trash-alt"></i> Delete Selected
        </button>
    </div>


    <div class="card trash-card">

        <div class="card-header bg-primary text-white">
            <i class="fas fa-database"></i> {{ $title }} — Deleted Records
        </div>

        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="40">
                            <input type="checkbox" onclick="toggleAll('{{ $title }}')" id="selectAll-{{ $title }}">
                        </th>
                        <th>#</th>
                        <th>Name / Code</th>
                        <th>Deleted At</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($rows as $key => $item)
                <tr id="row-{{ $title }}-{{ $item->id }}">

                    <td>
                        <input type="checkbox" class="row-check-{{ $title }}" value="{{ $item->id }}">
                    </td>

                    <td>{{ $key + 1 }}</td>

                    <td class="fw-bold text-primary">
                        {{ $item->name 
                            ?? $item->employee_name 
                            ?? $item->emp_name
                            ?? $item->vehicle_no 
                            ?? $item->code 
                            ?? $item->location
                            ?? 'N/A' }}
                    </td>

                    <td>{{ $item->deleted_at->format('d M Y h:i A') }}</td>

                    <td>
                        <button class="btn btn-success btn-sm"
                            onclick="restoreItem('{{ $title }}', '{{ $item->id }}')">
                            <i class="fas fa-undo"></i> Restore
                        </button>

                        <button class="btn btn-danger btn-sm"
                            onclick="deleteItem('{{ $title }}', '{{ $item->id }}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>

                </tr>
                @empty

                <tr>
                    <td colspan="5" class="text-center text-muted p-4">
                        <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                        No deleted {{ $title }} records.
                    </td>
                </tr>

                @endforelse

                </tbody>
            </table>
        </div>
    </div>

</div>
@endforeach

</div>

@endsection




@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

function toggleAll(model) {
    let m = document.getElementById(`selectAll-${model}`);
    document.querySelectorAll(`.row-check-${model}`).forEach(c => c.checked = m.checked);
}

function removeRow(model, id) {
    let r = document.getElementById(`row-${model}-${id}`);
    r.classList.add("fade-out");
    setTimeout(() => r.remove(), 550);
}

/* Restore Single */
function restoreItem(model, id) {
    fetch(`/api/trash/${model}/${id}/restore`, { method:"POST" })
    .then(r => r.json())
    .then(d => {
        Swal.fire("Restored!", d.success, "success");
        removeRow(model, id);
    });
}

/* Delete Single */
function deleteItem(model, id) {
    Swal.fire({
        icon:"warning", title:"Delete permanently?", showCancelButton:true
    }).then(res => {
        if (!res.isConfirmed) return;

        fetch(`/api/trash/${model}/${id}/delete`, { method:"DELETE" })
        .then(r => r.json())
        .then(d => {
            Swal.fire("Deleted!", d.success, "success");
            removeRow(model, id);
        });
    });
}

/* BULK RESTORE */
function bulkRestore(model) {
    let ids = [...document.querySelectorAll(`.row-check-${model}:checked`)]
        .map(c => c.value);

    if (!ids.length)
        return Swal.fire("Nothing Selected", "Select at least one row.", "info");

    ids.forEach(id => restoreItem(model, id));
}

/* BULK DELETE */
function bulkDelete(model) {
    let ids = [...document.querySelectorAll(`.row-check-${model}:checked`)]
        .map(c => c.value);

    if (!ids.length)
        return Swal.fire("Nothing Selected", "Select at least one row.", "info");

    Swal.fire({
        icon: "warning",
        title: "Delete selected records?",
        text: `${ids.length} records will be permanently deleted.`,
        showCancelButton: true
    }).then(res => {
        if (!res.isConfirmed) return;

        // 🔥 Delete all without extra popups
        ids.forEach(id => {
            fetch(`/api/trash/${model}/${id}/delete`, { method: "DELETE" })
            .then(r => r.json())
            .then(d => {
                removeRow(model, id);
            });
        });

        Swal.fire("Deleted!", "Selected records deleted.", "success");
    });
}


</script>
@stop
