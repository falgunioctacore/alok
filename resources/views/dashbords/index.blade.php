@extends('layouts.app')

@section('title', 'Dashboard')

@section('content_header')
<h1 class="text-primary font-weight-bold">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</h1>
@stop

@section('content')

<style>
    .dash-card {
        border-radius: 18px !important;
        transition: 0.3s;
        cursor: pointer;
        overflow: hidden;
        position: relative;
    }
    .dash-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 28px rgba(0,0,0,0.15);
    }
    .dash-icon {
        font-size: 45px;
        opacity: 0.3;
        position: absolute;
        right: 20px;
        bottom: 15px;
    }
    .gradient-1 { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
    .gradient-2 { background: linear-gradient(135deg, #43cea2, #185a9d); color: white; }
    .gradient-3 { background: linear-gradient(135deg, #ff9966, #ff5e62); color: white; }
    .gradient-4 { background: linear-gradient(135deg, #36d1dc, #5b86e5); color: white; }

    .section-title {
        font-size: 20px;
        font-weight: bold;
        margin: 25px 0 15px;
        color: #444;
    }
</style>


<div class="row">

    {{-- EMPLOYEE --}}
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('employees.webIndex') }}">
            <div class="card dash-card gradient-1">
                <div class="card-body">
                    <h3>{{ $counts['employees'] ?? 0 }}</h3>
                    <p class="mb-0">Total Employees</p>
                    <i class="fas fa-users dash-icon"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- ATTENDANCE --}}
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('attendance.webIndex') }}">
            <div class="card dash-card gradient-2">
                <div class="card-body">
                    <h3>{{ $counts['attendance_today'] ?? 0 }}</h3>
                    <p class="mb-0">Today Attendance</p>
                    <i class="fas fa-user-check dash-icon"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- VEHICLES --}}
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('vehicles.webIndex') }}">
            <div class="card dash-card gradient-3">
                <div class="card-body">
                    <h3>{{ $counts['vehicles'] ?? 0 }}</h3>
                    <p class="mb-0">Registered Vehicles</p>
                    <i class="fas fa-truck-moving dash-icon"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- GATES --}}
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('gates.webIndex') }}">
            <div class="card dash-card gradient-4">
                <div class="card-body">
                    <h3>{{ $counts['gates'] ?? 0 }}</h3>
                    <p class="mb-0">Active Gates</p>
                    <i class="fas fa-door-open dash-icon"></i>
                </div>
            </div>
        </a>
    </div>

</div>


{{-- ===================== CHART SECTION ========================= --}}
<h3 class="section-title">Analytics Overview</h3>

<div class="row">

    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                Attendance Overview
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="140"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                Employee by Plant
            </div>
            <div class="card-body">
                <canvas id="plantChart" height="140"></canvas>
            </div>
        </div>
    </div>

</div>

@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Attendance Chart
    new Chart(document.getElementById('attendanceChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($attendanceWeekly['labels']) !!},
            datasets: [{
                label: 'Attendance',
                data: {!! json_encode($attendanceWeekly['data']) !!},
                borderWidth: 3,
                tension: 0.4
            }]
        }
    });

    // Plant Employees Chart
    new Chart(document.getElementById('plantChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($employeesByPlant['labels']) !!},
            datasets: [{
                label: 'Employees',
                data: {!! json_encode($employeesByPlant['data']) !!},
                borderWidth: 2
            }]
        }
    });
</script>

@endpush