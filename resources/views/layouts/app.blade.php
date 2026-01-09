@extends('adminlte::page')

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@stop

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')
            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif

    <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-sm mr-3">
            <i class="fas fa-arrow-left"></i> Back
    </a>
@stop

@section('content')
    @yield('content_body')
@stop

@section('footer')
    <div class="w-100 text-right">
        Developed by 
        <a href="{{ config('app.company_url', 'https://www.octacoretechnologies.com/') }}" target="_blank">
            OctaCore Technologies
        </a>.
    </div>
@stop

{{-- ---------------------------------------------------------
    CSS
---------------------------------------------------------- --}}
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<style>
/* Pagination container */
.dataTables_wrapper .dataTables_paginate {
    margin-top: 15px !important;
    display: flex;
    justify-content: end;
    align-items: center;
}

/* Page buttons */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 12px;
    margin: 0 3px;
    border: 1px solid #d1d1d1 !important;
    border-radius: 6px !important;
    background: #fff !important;
    color: #333 !important;
    cursor: pointer;
}

/* Hover */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #e9ecef !important;
    border-color: #bcbcbc !important;
    color: #000 !important;
}

/* Active page */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #0d6efd !important;       /* Bootstrap primary */
    border-color: #0d6efd !important;
    color: #fff !important;
    border-radius: 6px !important;
}

/* Disabled buttons */
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    color: #aaa !important;
    cursor: not-allowed;
    opacity: 0.6 !important;
}

/* Remove weird shadows */
.dataTables_wrapper .dataTables_paginate .paginate_button:focus {
    outline: none !important;
    box-shadow: none !important;
}

    .compulsory {
        color: red;
    }

    /* Sidebar */
    .main-sidebar {
        background: linear-gradient(180deg, #032854 0%, #0d4ea2 100%) !important;
    }

    .main-sidebar .nav-link {
        color: #ffffff !important;
        transition: background 0.3s ease;
    }
    .main-sidebar .nav-link.active {
        background-color: #0d4ea2 !important;
        color: #ffffff !important;
    }

    /* Brand Logo */
    .brand-link {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 15px 0 !important;
        background-color: #032854 !important;
    }

    .brand-link img {
        max-height: 55px;
        width: auto;
    }

    .logo-xl {
        width: 200px !important;
        height: auto !important;
        max-height: 120px !important;
        object-fit: contain !important;
    }

    .sidebar {
        padding-top: 1rem;
    }
</style>
@endpush


{{-- ---------------------------------------------------------
    JAVASCRIPT
---------------------------------------------------------- --}}
@push('js')



<script>
$(document).ready(function() {

    // Edit Confirmation
    $('.edit').on('click', function (e) {
        if (!confirm('Are you sure you want to edit this?')) {
            e.preventDefault();
        }
    });

    // Delete Confirmation
    $('.delete').on('click', function (e) {
        if (!confirm('Are you sure you want to delete this?')) {
            e.preventDefault();
        }
    });

});

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

@endpush
