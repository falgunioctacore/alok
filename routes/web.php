<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendenaceController;
use App\Http\Controllers\Auth\AuthEmployeeController;
use App\Http\Controllers\DashBordController;
use App\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\GeoFencingPointController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\SiteAreaController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\web\PlantController as WebPlantController;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\GeoFencingPoint;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashbord');
    Route::get('/dashboard', [DashBordController::class, 'index']);

    Route::post('/vehicles/import', [VehicleController::class, 'import'])->name('vehicles.import');

    Route::post('/plants/import', [WebPlantController::class, 'import'])->name('plants.import');

    Route::resource('/plants', WebPlantController::class);
    Route::get('/vehicles', [VehicleController::class, 'webIndex'])->name('vehicles.webIndex');
    Route::get('/employees', [EmployeeController::class, 'webIndex'])->name('employees.webIndex');
    Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/departments', [DepartmentController::class, 'webIndex'])->name('departments.webIndex');
    Route::post('/departments/import', [DepartmentController::class, 'import'])->name('departments.import');
    Route::get('/department/export', [DepartmentController::class, 'downloadExport'])->name('department.export');
    Route::get('/department/template/download', function () {
        $path = public_path('templates/department_import_template.csv');
        if (file_exists($path)) {
            return response()->download($path, 'department_import_template.csv');
        }
        abort(404);
    })->name('department.template');
    Route::get('/site-areas', [SiteAreaController::class, 'webIndex'])->name('siteareas.webIndex');
    Route::get('/attendence', [AttendenaceController::class, 'webIndex'])->name('attendance.webIndex');

    //   Route::redirect('/','/mobile-attendence');
    Route::redirect('/', '/dashboard');
    Route::get('/emp-login', [AuthEmployeeController::class, 'showLoginForm']);

    //  Route::apiResource('access-points', GeoFencingPointController::class);
    //    Route::apiResource('employees', EmployeeController::class);
    //    Route::apiResource('vehicles', VehicleController::class);

    Route::get('/access-points', [GeoFencingPointController::class, 'webIndex'])->name('gates.webIndex');
    Route::get('/employees/report', [EmployeeController::class, 'reportIndex']);
    Route::get('/employees/{id}/show-page', function ($id) {
        return view('employees.show', ['id' => $id]);
    })->name('employees.showPage');

    Route::get('/vehicles/{id}/show-page', function ($id) {
        return view('vehicle.show', ['id' => $id]);
    })->name('vehicles.showPage');

    Route::get('/vehicle/report', [VehicleController::class, 'reportIndex']);

    Route::prefix('/trash')->controller(TrashController::class)->group(function () {
        Route::get('/index', 'index');
        Route::get('/restore/{model}/{id}', 'restore')->name('trash.restore');
        Route::get('/delete/{model}/{id}', 'forceDelete')->name('trash.delete');
    });


    Route::get('/employee/template/download', [EmployeeController::class, 'downloadTemplate'])->name('employee.template');
    Route::get('/vehicle/template/download', [VehicleController::class, 'downloadTemplate'])->name('vehicle.template');
    Route::get('/plant/template/download', function () {
        $path = public_path('templates/plant_import_template.csv');
        if (file_exists($path)) {
            return response()->download($path, 'plant_import_template.csv');
        }
        abort(404);
    })->name('plant.template');
    Route::get('/plant/export', [WebPlantController::class, 'downloadTemplate'])->name('plant.export');
});
Auth::routes([
    'register' => false
]);

Route::get('/mobile-attendence', [AttendenaceController::class, 'markPage']);


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
