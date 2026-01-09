<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\GeoFencingPoint;
use App\Models\Plant;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DashBordController extends Controller
{
    public function index()
{
    return view('dashbords.index', [
        'counts' => [
            'employees' => Employee::count(),
            'attendance_today' => Attendance::where('type','in')->whereDate('attendance_date', today())->count(),
            'vehicles' => Vehicle::count(),
            'gates' => GeoFencingPoint::count(),
        ],

        'attendanceWeekly' => [
            'labels' => ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            'data' => [
                Attendance::whereDate('attendance_date', now()->startOfWeek())->count(),
                Attendance::whereDate('attendance_date', now()->startOfWeek()->addDays(1))->count(),
                Attendance::whereDate('attendance_date', now()->startOfWeek()->addDays(2))->count(),
                Attendance::whereDate('attendance_date', now()->startOfWeek()->addDays(3))->count(),
                Attendance::whereDate('attendance_date', now()->startOfWeek()->addDays(4))->count(),
                Attendance::whereDate('attendance_date', now()->startOfWeek()->addDays(5))->count(),
                Attendance::whereDate('attendance_date', now()->startOfWeek()->addDays(6))->count(),
            ]
        ],

        'employeesByPlant' => [
            'labels' => Plant::pluck('name'),
            'data' => Plant::withCount('employees')->pluck('employees_count'),
        ]
    ]);
}

}
